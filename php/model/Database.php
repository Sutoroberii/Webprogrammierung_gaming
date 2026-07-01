<?php

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection !== null) {
            return self::$connection;
        }

        global $abs_path;

        if (!isset($abs_path)) {
            $pathResult = require __DIR__ . "/../../path.php";

            if (!isset($abs_path)) {
                $abs_path = is_string($pathResult) ? $pathResult : dirname(__DIR__, 2);
            }
        }

        $dbDir = $abs_path . "/db";

        if (!is_dir($dbDir) && !mkdir($dbDir, 0775, true) && !is_dir($dbDir)) {
            throw new RuntimeException("Datenbankordner konnte nicht erstellt werden.");
        }

        $dbFile = $dbDir . "/app.db";
        $db = new PDO("sqlite:" . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("PRAGMA foreign_keys = ON");
        $db->exec("PRAGMA busy_timeout = 5000");

        self::createTables($db);
        self::seedDatabase($db);

        self::$connection = $db;
        return self::$connection;
    }

    private static function createTables(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                username TEXT PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                creation_date TEXT NOT NULL
            );
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                media TEXT,
                text TEXT NOT NULL,
                url TEXT NOT NULL UNIQUE,
                author TEXT NOT NULL,
                created_at INTEGER NOT NULL,
                FOREIGN KEY (author)
                    REFERENCES users(username)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE
            );
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS post_tags (
                post_id INTEGER NOT NULL,
                tag TEXT NOT NULL,
                PRIMARY KEY (post_id, tag),
                FOREIGN KEY (post_id)
                    REFERENCES posts(id)
                    ON DELETE CASCADE
            );
        ");

        $db->exec("CREATE INDEX IF NOT EXISTS idx_posts_author ON posts(author);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_post_tags_tag ON post_tags(tag);");
    }

    private static function seedDatabase(PDO $db): void {
        $userCount = (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

        if ($userCount === 0) {
            $stmt = $db->prepare("
                INSERT INTO users (username, email, password_hash, creation_date)
                VALUES (:username, :email, :password_hash, :creation_date)
            ");

            $stmt->execute([
                ':username' => 'testuser',
                ':email' => 'test@example.de',
                ':password_hash' => password_hash('test123', PASSWORD_DEFAULT),
                ':creation_date' => date('Y-m-d H:i:s')
            ]);
        }

        $postCount = (int) $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();

        if ($postCount === 0) {
            $stmt = $db->prepare("
                INSERT INTO posts (title, media, text, url, author, created_at)
                VALUES (:title, :media, :text, :url, :author, :created_at)
            ");

            $stmt->execute([
                ':title' => 'Minecraft',
                ':media' => null,
                ':text' => 'Das ist ein Testbeitrag aus der Datenbank.',
                ':url' => 'minecraft-testbeitrag',
                ':author' => 'testuser',
                ':created_at' => time()
            ]);

            $postId = (int) $db->lastInsertId();
            $tagStmt = $db->prepare("INSERT OR IGNORE INTO post_tags (post_id, tag) VALUES (:post_id, :tag)");

            foreach (['minecraft', 'survival'] as $tag) {
                $tagStmt->execute([
                    ':post_id' => $postId,
                    ':tag' => $tag
                ]);
            }
        }
    }
}