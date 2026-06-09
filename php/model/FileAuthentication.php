<?php

require_once __DIR__ . "/AuthenticationUserData.php";
require_once __DIR__ . "/AuthenticationDao.php";

class FileAuthentication implements AuthenticationDao
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        // Ensure the directory exists
        if (!is_dir($this->filePath)) {
            mkdir($this->filePath, 0755, true);
        }
    }

    // Helper: Clean username to allow only safe characters
    private function cleanUsername(string $username): string
    {
        return preg_replace('/[^a-z0-9_-]/', '', basename($username));
    }

    // Helper: Path for a given username file
    private function getUserPath(string $username): string
    {
        return $this->filePath . "/" . $this->cleanUsername($username) . ".json";
    }

    // Helper: Load user data from a file
    private function loadUserFile(string $username): ?array
    {
        $path = $this->getUserPath($username);

        if (!file_exists($path)) {
            return null;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    public function createUser(string $username, string $email, string $passwordHash): void
    {
        $username = $this->cleanUsername($username);

        $data = [
            'username' => $username,
            'email' => strtolower(trim($email)),
            'passwordHash' => $passwordHash,
            'creationDate' => date('Y-m-d H:i:s')
        ];

        file_put_contents($this->getUserPath($username), json_encode($data));
    }

    public function deleteUser(string $username): void
    {
        $path = $this->getUserPath($username);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getUserByUsername(string $username): ?AuthenticationUserData
    {
        $data = $this->loadUserFile($username);

        if ($data === null) {
            return null;
        }

        return new AuthenticationUserData(
            $data['username'] ?? '',
            $data['email'] ?? '',
            $data['passwordHash'] ?? '',
            $data['creationDate'] ?? ''
        );
    }

    public function getUserByEmail(string $email): ?AuthenticationUserData
    {
        $email = strtolower(trim($email));

        if ($email === '') {
            return null;
        }

        foreach (glob($this->filePath . "/*.json") as $file) {
            $data = json_decode(file_get_contents($file), true);

            if (!is_array($data)) {
                continue;
            }

            if (strtolower(trim($data['email'] ?? '')) === $email) {
                return new AuthenticationUserData(
                    $data['username'] ?? '',
                    $data['email'] ?? '',
                    $data['passwordHash'] ?? '',
                    $data['creationDate'] ?? ''
                );
            }
        }

        return null;
    }

    public function usernameAlreadyTaken(string $username): bool
    {
        return file_exists($this->getUserPath($username));
    }

    public function emailAlreadyTaken(string $email): bool
    {
        return $this->getUserByEmail($email) !== null;
    }

    public function updateUsername(string $oldUsername, string $newUsername): void
    {
        $oldPath = $this->getUserPath($oldUsername);

        if (!file_exists($oldPath) || $oldUsername === $newUsername) {
            return;
        }

        $data = $this->loadUserFile($oldUsername);

        if ($data === null) {
            return;
        }

        $data['username'] = $this->cleanUsername($newUsername);

        file_put_contents($this->getUserPath($newUsername), json_encode($data));
        unlink($oldPath);
    }

    public function updateEmail(string $username, string $email): void
    {
        $path = $this->getUserPath($username);

        if (!file_exists($path)) {
            return;
        }

        $data = $this->loadUserFile($username);

        if ($data === null) {
            return;
        }

        $data['email'] = strtolower(trim($email));

        file_put_contents($path, json_encode($data));
    }

    public function updatePassword(string $username, string $newHash): void
    {
        $path = $this->getUserPath($username);

        if (!file_exists($path)) {
            return;
        }

        $data = $this->loadUserFile($username);

        if ($data === null) {
            return;
        }

        $data['passwordHash'] = $newHash;

        file_put_contents($path, json_encode($data));
    }
}