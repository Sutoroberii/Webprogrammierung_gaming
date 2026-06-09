<?php

require_once __DIR__ . "/AuthenticationDao.php";
require_once __DIR__ . "/AuthenticationUserData.php";

class AuthenticationPDOSQLite implements AuthenticationDao {
    private static $instance = null;

    public static function getInstance(): AuthenticationPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new AuthenticationPDOSQLite();
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

    public function createUser(string $username, string $email, string $passwordHash): void {
        $db = $this->getConnection();

        try {
            $db->beginTransaction();

            $check = $db->prepare("
                SELECT 1
                FROM users
                WHERE username = :username OR email = :email
                LIMIT 1
            ");
            $check->execute([
                ":username" => $username,
                ":email" => strtolower(trim($email))
            ]);

            if ($check->fetchColumn() !== false) {
                $db->rollBack();
                throw new RuntimeException("Benutzername oder E-Mail ist bereits vergeben.");
            }

            $stmt = $db->prepare("
                INSERT INTO users (username, email, password_hash, creation_date)
                VALUES (:username, :email, :password_hash, :creation_date)
            ");

            $stmt->execute([
                ":username" => $username,
                ":email" => strtolower(trim($email)),
                ":password_hash" => $passwordHash,
                ":creation_date" => date("Y-m-d H:i:s")
            ]);

            $db->commit();
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw new RuntimeException("Datenbankfehler beim Erstellen des Benutzers.");
        }
    }

    public function deleteUser(string $username): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                DELETE FROM users
                WHERE username = :username
            ");

            $stmt->execute([":username" => $username]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Löschen des Benutzers.");
        }
    }

    public function getUserByUsername(string $username): ?AuthenticationUserData {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT *
                FROM users
                WHERE username = :username
                LIMIT 1
            ");

            $stmt->execute([":username" => $username]);
            $row = $stmt->fetch();

            return $row ? $this->rowToAuthenticationUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen des Benutzers.");
        }
    }

    public function getUserByEmail(string $email): ?AuthenticationUserData {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                SELECT *
                FROM users
                WHERE email = :email
                LIMIT 1
            ");

            $stmt->execute([":email" => strtolower(trim($email))]);
            $row = $stmt->fetch();

            return $row ? $this->rowToAuthenticationUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Lesen des Benutzers.");
        }
    }

    public function usernameAlreadyTaken(string $username): bool {
        return $this->getUserByUsername($username) !== null;
    }

    public function emailAlreadyTaken(string $email): bool {
        return $this->getUserByEmail($email) !== null;
    }

    public function updateUsername(string $oldUsername, string $newUsername): void {
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
            throw new RuntimeException("Datenbankfehler beim Ändern des Benutzernamens.");
        }
    }

    public function updateEmail(string $username, string $email): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                UPDATE users
                SET email = :email
                WHERE username = :username
            ");

            $stmt->execute([
                ":email" => strtolower(trim($email)),
                ":username" => $username
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Ändern der E-Mail.");
        }
    }

    public function updatePassword(string $username, string $newHash): void {
        try {
            $db = $this->getConnection();

            $stmt = $db->prepare("
                UPDATE users
                SET password_hash = :password_hash
                WHERE username = :username
            ");

            $stmt->execute([
                ":password_hash" => $newHash,
                ":username" => $username
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Datenbankfehler beim Ändern des Passworts.");
        }
    }

    private function rowToAuthenticationUser(array $row): AuthenticationUserData {
        return new AuthenticationUserData(
            $row["username"],
            $row["email"],
            $row["password_hash"],
            $row["creation_date"]
        );
    }
}
