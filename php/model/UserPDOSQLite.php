<?php

require_once __DIR__ . "/UserDao.php";
require_once __DIR__ . "/UserData.php";
require_once __DIR__ . "/Database.php";

class UserPDOSQLite implements UserDAO {
    private static ?UserPDOSQLite $instance = null;

    public static function getInstance(): UserPDOSQLite {
        if (self::$instance === null) {
            self::$instance = new UserPDOSQLite();
        }

        return self::$instance;
    }

    private function getConnection(): PDO {
        return Database::getConnection();
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
            $stmt->execute([':username' => $username]);
            $row = $stmt->fetch();

            return $row ? $this->rowToUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Lesen des Profils.');
        }
    }

    public function search(string $query): array {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("
                SELECT username, email, creation_date
                FROM users
                WHERE username LIKE :query OR email LIKE :query
                ORDER BY username ASC
            ");
            $stmt->execute([':query' => '%' . trim($query) . '%']);

            $users = [];
            foreach ($stmt->fetchAll() as $row) {
                $users[] = $this->rowToUser($row);
            }

            return $users;
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler bei der Benutzersuche.');
        }
    }

    public function updateProfile(string $username, array $data): void {
        if (!isset($data['email'])) {
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
                ':email' => strtolower(trim($data['email'])),
                ':username' => $username
            ]);
        } catch (PDOException $e) {
            if ($this->isUniqueViolation($e)) {
                throw new RuntimeException('E-Mail ist bereits vergeben.');
            }

            throw new RuntimeException('Datenbankfehler beim Aktualisieren des Profils.');
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
                ':newUsername' => trim($newUsername),
                ':oldUsername' => $oldUsername
            ]);
        } catch (PDOException $e) {
            if ($this->isUniqueViolation($e)) {
                throw new RuntimeException('Benutzername ist bereits vergeben.');
            }

            throw new RuntimeException('Datenbankfehler beim Umbenennen des Profils.');
        }
    }

    public function deleteProfile(string $username): void {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("DELETE FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
        } catch (PDOException $e) {
            throw new RuntimeException('Datenbankfehler beim Löschen des Profils.');
        }
    }

    public function createProfile(string $username, string $email): void {
        // Profil und Authentifizierung liegen jetzt in derselben users-Tabelle.
        // Der Benutzer wird bereits über AuthenticationPDOSQLite::createUser() angelegt.
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
            throw new RuntimeException('Datenbankfehler beim Erstellen des Profils.');
        }
    }

    private function rowToUser(array $row): UserData {
        return new UserData(
            $row['username'],
            $row['email'],
            $row['creation_date']
        );
    }

    private function isUniqueViolation(PDOException $e): bool {
        return $e->getCode() === '23000'
            || str_contains($e->getMessage(), 'UNIQUE constraint failed');
    }
}