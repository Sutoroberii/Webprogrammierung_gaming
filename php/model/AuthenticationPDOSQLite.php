<?php

require_once __DIR__ . "/AuthenticationDao.php";
require_once __DIR__ . "/AuthenticationUserData.php";
require_once __DIR__ . "/Database.php";

class AuthenticationPDOSQLite implements AuthenticationDao {
    private static ?AuthenticationPDOSQLite $instance = null;

    public static function getInstance(): AuthenticationPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new AuthenticationPDOSQLite();
        }

        return self::$instance;
    }

    private function getConnection(): PDO {
        return Database::getConnection();
    }

    public function createUser(string $username, string $email, string $passwordHash): void {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("
                INSERT INTO users (username, email, password_hash, creation_date)
                VALUES (:username, :email, :password_hash, :creation_date)
            ");

            $stmt->execute([
                ':username' => trim($username),
                ':email' => strtolower(trim($email)),
                ':password_hash' => $passwordHash,
                ':creation_date' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            if ($this->isUniqueViolation($e)) {
                throw new RuntimeException('Benutzername oder E-Mail ist bereits vergeben.');
            }

            throw new RuntimeException('Datenbankfehler beim Erstellen des Benutzers.');
        }
    }

    public function deleteUser(string $username): void {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("DELETE FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Löschen des Benutzers.');
        }
    }

    public function getUserByUsername(string $username): ?AuthenticationUserData {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $row = $stmt->fetch();

            return $row ? $this->rowToAuthenticationUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen des Benutzers.');
        }
    }

    public function getUserByEmail(string $email): ?AuthenticationUserData {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => strtolower(trim($email))]);
            $row = $stmt->fetch();

            return $row ? $this->rowToAuthenticationUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen des Benutzers.');
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
                ':newUsername' => trim($newUsername),
                ':oldUsername' => $oldUsername
            ]);
        } catch (PDOException $e) {
            if ($this->isUniqueViolation($e)) {
                throw new RuntimeException('Benutzername ist bereits vergeben.');
            }

            throw new RuntimeException('Datenbankfehler beim Ändern des Benutzernamens.');
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
                ':email' => strtolower(trim($email)),
                ':username' => $username
            ]);
        } catch (PDOException $e) {
            if ($this->isUniqueViolation($e)) {
                throw new RuntimeException('E-Mail ist bereits vergeben.');
            }

            throw new RuntimeException('Datenbankfehler beim Ändern der E-Mail.');
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
                ':password_hash' => $newHash,
                ':username' => $username
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Ändern des Passworts.');
        }
    }

    private function rowToAuthenticationUser(array $row): AuthenticationUserData {
        return new AuthenticationUserData(
            $row['username'],
            $row['email'],
            $row['password_hash'],
            $row['creation_date']
        );
    }

    private function isUniqueViolation(PDOException $e): bool {
        return $e->getCode() === '23000'
            || str_contains($e->getMessage(), 'UNIQUE constraint failed');
    }
}