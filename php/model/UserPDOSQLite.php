<?php

require_once __DIR__ . "/UserDao.php";
require_once __DIR__ . "/UserData.php";

class UserPDOSQLite implements UserDAO {
    private static $instance = null;

    public static function getInstance(): UserPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new UserPDOSQLite();
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

        $dbFile = $dbDir . "/user.db";
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
            INSERT INTO users (username, email, password_hash, creation_date)
            VALUES (:username, :email, :password_hash, :creation_date)
        ");

        $stmt->execute([
            ":username" => "testuser",
            ":email" => "test@example.de",
            ":password_hash" => password_hash("test123", PASSWORD_DEFAULT),
            ":creation_date" => date("Y-m-d H:i:s")
        ]);
    }

    private function createTables(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                username TEXT PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                creation_date TEXT NOT NULL
            );
        ");
    }

    public function getByUsername(string $username): ?UserData {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT username, email, creation_date
                FROM users
                WHERE username = :username
                LIMIT 1
            ");

            $stmt->execute([":username" => $username]);
            $row = $stmt->fetch();

            return $row ? $this->rowToUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen des Profils.");
        }
    }

    public function search(string $query): array {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT username, email, creation_date
                FROM users
                WHERE username LIKE :query
                   OR email LIKE :query
                ORDER BY username ASC
            ");

            $stmt->execute([":query" => "%" . $query . "%"]);

            $users = [];
            foreach ($stmt->fetchAll() as $row) {
                $users[] = $this->rowToUser($row);
            }

            return $users;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler bei der Benutzersuche.");
        }
    }

    public function updateProfile(string $username, array $data): void {
        if (!isset($data["email"])) {
            return;
        }

        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                UPDATE users
                SET email = :email
                WHERE username = :username
            ");

            $stmt->execute([
                ":email" => strtolower(trim($data["email"])),
                ":username" => $username
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Aktualisieren des Profils.");
        }
    }

    public function renameProfile(string $oldUsername, string $newUsername): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                UPDATE users
                SET username = :newUsername
                WHERE username = :oldUsername
            ");

            $stmt->execute([
                ":newUsername" => $newUsername,
                ":oldUsername" => $oldUsername
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Umbenennen des Profils.");
        }
    }

    public function deleteProfile(string $username): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                DELETE FROM users
                WHERE username = :username
            ");

            $stmt->execute([":username" => $username]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Löschen des Profils.");
        }
    }

    public function createProfile(string $username, string $email): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                INSERT OR IGNORE INTO users (username, email, password_hash, creation_date)
                VALUES (:username, :email, :password_hash, :creation_date)
            ");

            $stmt->execute([
                ":username" => $username,
                ":email" => strtolower(trim($email)),
                ":password_hash" => "",
                ":creation_date" => date("Y-m-d H:i:s")
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Erstellen des Profils.");
        }
    }

    private function rowToUser(array $row): UserData {
        return new UserData(
            $row["username"],
            $row["email"],
            $row["creation_date"]
        );
    }
}
