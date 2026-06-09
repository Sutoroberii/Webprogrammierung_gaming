<?php

require_once __DIR__ . "/PostDAO.php";
require_once __DIR__ . "/PostData.php";
require_once __DIR__ . "/PostQuery.php";
require_once __DIR__ . "/PostQueryResult.php";

class PostPDOSQLite implements PostDAO {
    private static $instance = null;

    public static function getInstance(): PostPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new PostPDOSQLite();
        }
        return self::$instance;
    }

    private function getConnection(): PDO {
        global $abs_path;

        if (!isset($abs_path)) {
            $pathResult = require __DIR__ . "/../../path.php";
            if (!isset($abs_path)) {
                $abs_path = is_string($pathResult) ? $pathResult : dirname(__DIR__, 2);
            }
        }

        $dbDir = $abs_path . "/db";
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0775, true);
        }

        $dbFile = $dbDir . "/post.db";
        $isNewDatabase = !file_exists($dbFile);

        $db = new PDO("sqlite:" . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($isNewDatabase) {
            $this->createDatabase($db);
        } else {
            $this->createTables($db);
        }

        return $db;
    }

    private function createDatabase(PDO $db): void {
        $this->createTables($db);

        $stmt = $db->prepare("
            INSERT INTO posts (title, tags, media, text, url, author, created_at)
            VALUES (:title, :tags, :media, :text, :url, :author, :created_at)
        ");

        $stmt->execute([
            ":title" => "Minecraft",
            ":tags" => json_encode(["minecraft", "survival"]),
            ":media" => null,
            ":text" => "Das ist ein Testbeitrag aus der Datenbank.",
            ":url" => "minecraft-testbeitrag",
            ":author" => "testuser",
            ":created_at" => time()
        ]);
    }

    private function createTables(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                tags TEXT NOT NULL DEFAULT '[]',
                media TEXT,
                text TEXT NOT NULL,
                url TEXT NOT NULL UNIQUE,
                author TEXT,
                created_at INTEGER NOT NULL
            );
        ");
    }

    public function createPost(PostData $post): PostData {
        try {
            $db = $this->getConnection();

            $url = $this->createUniqueUrl($db, $post->getPostTitle());

            $stmt = $db->prepare("
                INSERT INTO posts (title, tags, media, text, url, author, created_at)
                VALUES (:title, :tags, :media, :text, :url, :author, :created_at)
            ");

            $createdAt = time();

            $stmt->execute([
                ":title" => $post->getPostTitle(),
                ":tags" => json_encode($post->getPostTags()),
                ":media" => $post->getPostMedia(),
                ":text" => $post->getPostText(),
                ":url" => $url,
                ":author" => $post->getPostAuthor(),
                ":created_at" => $createdAt
            ]);

            return new PostData(
                $post->getPostTitle(),
                $post->getPostTags(),
                $post->getPostMedia(),
                $post->getPostText(),
                $url,
                intval($db->lastInsertId()),
                $post->getPostAuthor(),
                $createdAt
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Erstellen des Beitrags.");
        }
    }

    public function deletePost(int $id): bool {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                DELETE FROM posts
                WHERE id = :id
            ");

            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Löschen des Beitrags.");
        }
    }

    public function updatePost(PostData $post): bool {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                UPDATE posts
                SET title = :title,
                    tags = :tags,
                    media = :media,
                    text = :text
                WHERE id = :id
            ");

            $stmt->execute([
                ":title" => $post->getPostTitle(),
                ":tags" => json_encode($post->getPostTags()),
                ":media" => $post->getPostMedia(),
                ":text" => $post->getPostText(),
                ":id" => $post->getPostId()
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Aktualisieren des Beitrags.");
        }
    }

    public function findByUrl(string $url): ?PostData {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT *
                FROM posts
                WHERE url = :url
                LIMIT 1
            ");

            $stmt->execute([":url" => $url]);
            $row = $stmt->fetch();

            return $row ? $this->rowToPost($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen des Beitrags.");
        }
    }

    public function findbyId(int $id): ?PostData {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT *
                FROM posts
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute([":id" => $id]);
            $row = $stmt->fetch();

            return $row ? $this->rowToPost($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen des Beitrags.");
        }
    }

    public function findAll(): array {
        try {
            $db = $this->getConnection();

            $stmt = $db->query("
                SELECT *
                FROM posts
                ORDER BY created_at DESC
            ");

            $posts = [];
            foreach ($stmt->fetchAll() as $row) {
                $posts[] = $this->rowToPost($row);
            }

            return $posts;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen der Beiträge.");
        }
    }

    public function query(PostQuery $query): PostQueryResult {
        try {
            $db = $this->getConnection();

            $where = [];
            $params = [];

            if ($query->getWordsearch() !== null && trim($query->getWordsearch()) !== "") {
                $where[] = "(title LIKE :word OR text LIKE :word)";
                $params[":word"] = "%" . trim($query->getWordsearch()) . "%";
            }

            if ($query->getTagsearch() !== null && trim($query->getTagsearch()) !== "") {
                $where[] = "tags LIKE :tag";
                $params[":tag"] = "%" . trim($query->getTagsearch()) . "%";
            }

            if ($query->getAuthorsearch() !== null && trim($query->getAuthorsearch()) !== "") {
                $where[] = "author = :author";
                $params[":author"] = trim($query->getAuthorsearch());
            }

            $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

            $countStmt = $db->prepare("SELECT COUNT(*) FROM posts $whereSql");
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = intval($countStmt->fetchColumn());

            $orderBy = "created_at DESC";
            if ($query->getSort() === "oldest") {
                $orderBy = "created_at ASC";
            }

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

            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $posts = [];
            foreach ($stmt->fetchAll() as $row) {
                $posts[] = $this->rowToPost($row);
            }

            return new PostQueryResult($posts, $total, $query);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler bei der Beitragssuche.");
        }
    }

    public function getBestTags(int $limit = 10): array {
        $tagCounts = [];

        foreach ($this->findAll() as $post) {
            foreach ($post->getPostTags() as $tag) {
                $tag = trim(strtolower($tag));
                if ($tag === "") {
                    continue;
                }
                $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
            }
        }

        arsort($tagCounts);

        return array_slice(array_keys($tagCounts), 0, $limit);
    }

    private function rowToPost(array $row): PostData {
        return new PostData(
            $row["title"],
            json_decode($row["tags"], true) ?? [],
            $row["media"],
            $row["text"],
            $row["url"],
            intval($row["id"]),
            $row["author"],
            intval($row["created_at"])
        );
    }

    private function createUniqueUrl(PDO $db, string $title): string {
        $base = $this->urlFriendly($title);
        if ($base === "") {
            $base = "beitrag";
        }

        $url = $base;
        $counter = 1;

        while ($this->urlExists($db, $url)) {
            $counter++;
            $url = $base . "-" . $counter;
        }

        return $url;
    }

    private function urlExists(PDO $db, string $url): bool {
        $stmt = $db->prepare("
            SELECT 1
            FROM posts
            WHERE url = :url
            LIMIT 1
        ");

        $stmt->execute([":url" => $url]);

        return $stmt->fetchColumn() !== false;
    }

    private function urlFriendly(string $title): string {
        $title = strtolower(trim($title));
        $title = preg_replace("/[^a-z0-9äöüß]+/u", "-", $title);
        $title = trim($title, "-");

        return $title;
    }
}
