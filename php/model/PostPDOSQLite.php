<?php

require_once __DIR__ . "/PostDAO.php";
require_once __DIR__ . "/PostData.php";
require_once __DIR__ . "/PostQuery.php";
require_once __DIR__ . "/PostQueryResult.php";
require_once __DIR__ . "/Database.php";

class PostPDOSQLite implements PostDAO {
    private static ?PostPDOSQLite $instance = null;

    public static function getInstance(): PostPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new PostPDOSQLite();
        }

        return self::$instance;
    }

    private function getConnection(): PDO {
        return Database::getConnection();
    }

    public function createPost(PostData $post): PostData {
        $db = $this->getConnection();

        try {
            $db->beginTransaction();

            $author = trim((string) $post->getPostAuthor());
            if ($author === '') {
                throw new RuntimeException('Ein Beitrag benötigt einen Autor.');
            }

            $createdAt = time();
            $baseUrl = $this->urlFriendly($post->getPostTitle());
            if ($baseUrl === '') {
                $baseUrl = 'beitrag';
            }

            $postId = null;
            $url = $baseUrl;

            for ($counter = 1; $counter <= 50; $counter++) {
                $url = $counter === 1 ? $baseUrl : $baseUrl . '-' . $counter;

                try {
                    $stmt = $db->prepare("
                        INSERT INTO posts (title, media, text, url, author, created_at)
                        VALUES (:title, :media, :text, :url, :author, :created_at)
                    ");

                    $stmt->execute([
                        ':title' => $post->getPostTitle(),
                        ':media' => $post->getPostMedia(),
                        ':text' => $post->getPostText(),
                        ':url' => $url,
                        ':author' => $author,
                        ':created_at' => $createdAt
                    ]);

                    $postId = (int) $db->lastInsertId();
                    break;
                } catch (PDOException $e) {
                    if ($this->isUniqueUrlViolation($e)) {
                        continue;
                    }

                    throw $e;
                }
            }

            if ($postId === null) {
                throw new RuntimeException('Es konnte keine eindeutige Beitrags-URL erzeugt werden.');
            }

            $this->replaceTags($db, $postId, $post->getPostTags());
            $db->commit();

            return new PostData(
                $post->getPostTitle(),
                $post->getPostTags(),
                $post->getPostMedia(),
                $post->getPostText(),
                $url,
                $postId,
                $author,
                $createdAt
            );
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            if ($e instanceof RuntimeException) {
                throw $e;
            }

            if ($e instanceof PDOException && $this->isForeignKeyViolation($e)) {
                throw new RuntimeException('Der Autor des Beitrags existiert nicht.');
            }

            throw new RuntimeException('Datenbankfehler beim Erstellen des Beitrags.');
        }
    }

    public function deletePost(int $id): bool {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Löschen des Beitrags.');
        }
    }

    public function deletePostByAuthor(int $id, string $author): bool
    {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                DELETE FROM posts
                WHERE id = :id
                AND author = :author
            ");

            $stmt->execute([
                ':id' => $id,
                ':author' => $author
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Löschen des Beitrags.');
        }
    }

    public function updatePost(PostData $post): bool {
        $db = $this->getConnection();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                UPDATE posts
                SET title = :title,
                    media = :media,
                    text = :text
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $post->getPostTitle(),
                ':media' => $post->getPostMedia(),
                ':text' => $post->getPostText(),
                ':id' => $post->getPostId()
            ]);

            $updated = $stmt->rowCount() > 0;
            $this->replaceTags($db, (int) $post->getPostId(), $post->getPostTags());

            $db->commit();
            return $updated;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw new RuntimeException('Datenbankfehler beim Aktualisieren des Beitrags.');
        }
    }

    public function findByUrl(string $url): ?PostData {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT * FROM posts WHERE url = :url LIMIT 1");
            $stmt->execute([':url' => $url]);
            $row = $stmt->fetch();

            return $row ? $this->rowToPost($db, $row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen des Beitrags.');
        }
    }

    public function findbyId(int $id): ?PostData {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->rowToPost($db, $row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen des Beitrags.');
        }
    }

    public function findAll(): array {
        try {
            $db = $this->getConnection();
            $stmt = $db->query("SELECT * FROM posts ORDER BY created_at DESC");

            $posts = [];
            foreach ($stmt->fetchAll() as $row) {
                $posts[] = $this->rowToPost($db, $row);
            }

            return $posts;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen der Beiträge.');
        }
    }

    public function query(PostQuery $query): PostQueryResult {
        try {
            $db = $this->getConnection();
            $where = [];
            $params = [];

            if ($query->getWordsearch() !== null && trim($query->getWordsearch()) !== '') {
                $where[] = '(title LIKE :word OR text LIKE :word)';
                $params[':word'] = '%' . trim($query->getWordsearch()) . '%';
            }

            if ($query->getTagsearch() !== null && trim($query->getTagsearch()) !== '') {
                $where[] = "EXISTS (
                    SELECT 1
                    FROM post_tags pt
                    WHERE pt.post_id = posts.id AND pt.tag LIKE :tag
                )";
                $params[':tag'] = '%' . trim($query->getTagsearch()) . '%';
            }

            if ($query->getAuthorsearch() !== null && trim($query->getAuthorsearch()) !== '') {
                $where[] = 'author = :author';
                $params[':author'] = trim($query->getAuthorsearch());
            }

            $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

            $countStmt = $db->prepare("SELECT COUNT(*) FROM posts $whereSql");
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();

            $orderBy = $query->getSort() === 'oldest' ? 'created_at ASC' : 'created_at DESC';
            $limit = $query->getLimit();
            $offset = ($query->getPage() - 1) * $limit;

            $stmt = $db->prepare("
                SELECT *
                FROM posts
                $whereSql
                ORDER BY $orderBy
                LIMIT :limit OFFSET :offset
            ");

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $posts = [];
            foreach ($stmt->fetchAll() as $row) {
                $posts[] = $this->rowToPost($db, $row);
            }

            return new PostQueryResult($posts, $total, $query);
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler bei der Beitragssuche.');
        }
    }

    public function getBestTags(int $limit = 10): array {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("
                SELECT tag
                FROM post_tags
                GROUP BY tag
                ORDER BY COUNT(*) DESC, tag ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return array_column($stmt->fetchAll(), 'tag');
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen der Tags.');
        }
    }

    private function rowToPost(PDO $db, array $row): PostData {
        return new PostData(
            $row['title'],
            $this->loadTags($db, (int) $row['id']),
            $row['media'],
            $row['text'],
            $row['url'],
            (int) $row['id'],
            $row['author'],
            (int) $row['created_at']
        );
    }

    private function loadTags(PDO $db, int $postId): array {
        $stmt = $db->prepare("SELECT tag FROM post_tags WHERE post_id = :post_id ORDER BY tag ASC");
        $stmt->execute([':post_id' => $postId]);

        return array_column($stmt->fetchAll(), 'tag');
    }

    private function replaceTags(PDO $db, int $postId, array $tags): void {
        $deleteStmt = $db->prepare("DELETE FROM post_tags WHERE post_id = :post_id");
        $deleteStmt->execute([':post_id' => $postId]);

        $insertStmt = $db->prepare("INSERT OR IGNORE INTO post_tags (post_id, tag) VALUES (:post_id, :tag)");

        foreach ($tags as $tag) {
            $tag = trim(strtolower((string) $tag));

            if ($tag === '') {
                continue;
            }

            $insertStmt->execute([
                ':post_id' => $postId,
                ':tag' => $tag
            ]);
        }
    }

    

    private function urlFriendly(string $title): string {
        $title = strtolower(trim($title));
        $title = preg_replace('/[^a-z0-9äöüß]+/u', '-', $title);
        return trim($title, '-');
    }

    private function isUniqueUrlViolation(PDOException $e): bool {
        return str_contains($e->getMessage(), 'posts.url')
            || str_contains($e->getMessage(), 'UNIQUE constraint failed: posts.url');
    }

    private function isForeignKeyViolation(PDOException $e): bool {
        return str_contains($e->getMessage(), 'FOREIGN KEY constraint failed');
    }

    
}