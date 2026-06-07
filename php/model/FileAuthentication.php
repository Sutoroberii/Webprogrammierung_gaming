<?php
require_once __DIR__ ."/AuthenticationUserData.php";
require_once __DIR__ ."/AuthenticationDao.php";

class FileAuthentication implements AuthenticationDao {

    public function __construct(private string $filePath) {
    }

    public function clearUpUsername(string $username): string {
        return preg_replace('/[^a-z0-9_-]/', '', basename($username));
    }

    public function pathForUsername(string $username): string {
        return $this->filePath ."/". $this->clearUpUsername($username) .".json";
    }

    public function createUser(string $username, string $email, string $passwordHash): void {
        $username = $this->clearUpUsername($username);

        if (!is_dir($this->filePath)) {
            mkdir($this->filePath, 0755, true);
        }

        $data = ['username' => $username, 'email' => $email, 'passwordHash' => $passwordHash, 'creationDate' => date('Y-m-d H:i:s')];

        file_put_contents($this->pathForUsername($username), json_encode($data));
    }

    public function deleteUser(string $username): void {
        $username = $this->clearUpUsername($username);
        $path = $this->pathForUsername($username);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getUserByUsername(string $username): ?AuthenticationUserData {
        $username = $this->clearUpUsername($username);
        if ($username === null) {
            return null;
        }
        $path = $this->pathForUsername($username);
        if (!file_exists($path)) {
            return null;
        }
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            return null;
        }
        return new AuthenticationUserData($data['username'] ?? '', $data['email'] ?? '', $data['passwordHash'] ?? '', $data['creationDate'] ?? '');
    }

    public function getUserByEmail(string $email): ?AuthenticationUserData {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }
        foreach (glob($this->filePath ."/*.json") as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (!is_array($data)) {
                continue;
            }
            if (strtolower(trim($data["email"] ?? '')) === $email) {
                return new AuthenticationUserData($data['username'] ?? '', $data['email'] ?? '', $data['passwordHash'] ?? '', $data['creationDate'] ?? '');
            }
        }
        return null;
    }

    public function UsernameAlreadyTaken(string $username): bool {
        return file_exists($this->pathForUsername($username));
    }

    public function emailAlreadyTaken(string $email): bool {
        return $this->getUserByEmail($email) !== null;
    }

    public function updateUsername(string $oldUsername, string $newUsername): void {
        $oldPath = $this->pathForUsername($oldUsername);
        if ($oldUsername === $newUsername) {
            return;
        }
        if (!file_exists($oldPath)) {
            return;
        }
        $data = json_decode(file_get_contents($oldPath), true);
        if (!is_array($data)) {
            return;
        }
        $data['username'] = $this->clearUpUsername($newUsername);
        file_put_contents($this->pathForUsername($newUsername), json_encode($data));
        unlink($oldPath);
        return;
    }

    public function updateEmail(string $username, string $email): void {
        $path = $this->pathForUsername($username);
        if (!file_exists($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            return;
        }
        $data['email'] = strtolower(trim($email));
        file_put_contents($path, json_encode($data));
    }

    public function updatePassword(string $username, string $newHash): void {
        $path = $this->pathForUsername($username);
        if (!file_exists($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            return;
        }
        $data['passwordHash'] = $newHash;
        file_put_contents($path, json_encode($data));
    }











}