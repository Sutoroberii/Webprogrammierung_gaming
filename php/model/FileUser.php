<?php
require_once __DIR__ . "/UserDao.php";
require_once __DIR__ ."/UserData.php";

class FileUser implements UserDao {

    public function __construct(public string $filePath) {
    }

    public function clearUpUsername(string $username): string {
        return preg_replace('/[^a-z0-9_-]/', '', basename($username));
    }

    public function pathForUsername(string $username): string {
        return $this->filePath ."/". $this->clearUpUsername($username) .".json";
    }

    public function getByUsername(string $username): ?UserData {
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
        return new UserData($data['username'] ?? $username, $data['email'] ?? '', $data['creationDate'] ?? '');
    }

    public function search(string $query): array {
        $query = strtolower(trim($query));
        if ($query === '') {
            return [];
        }
        $results = [];
        foreach (glob($this->filePath ."/*.json") as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (!is_array($data)) {
                continue;
            }
            if (strpos(strtolower($data['username'] ?? ''), $query) !== false || strpos(strtolower($data['email'] ?? ''), $query) !== false) {
                $results[] = new UserData($data['username'] ?? '', $data['email'] ?? '', $data['creationDate'] ?? '');
            }
        }
        return $results;
    }

    public function updateProfile(string $username, array $data): void {
        $username = $this->clearUpUsername($username);
        $path = $this->pathForUsername($username);
        if (!file_exists($path)) {
            return;
        }
        $currentData = json_decode(file_get_contents($path), true);
        if (!is_array($currentData)) {
            return;
        }
        $updatedData = array_merge($currentData, $data);
        file_put_contents($path, json_encode($updatedData));
    }

    public function renameProfile(string $oldUsername, string $newUsername): void {
        $oldUsername = $this->clearUpUsername($oldUsername);
        $newUsername = $this->clearUpUsername($newUsername);
        $oldPath = $this->pathForUsername($oldUsername);
        $newPath = $this->pathForUsername($newUsername);
        if (!file_exists($oldPath) || file_exists($newPath)) {
            return;
        }
        $data = json_decode(file_get_contents($oldPath), true);
        if (!is_array($data)) {
            return;
        }
        $data['username'] = $newUsername;
        file_put_contents($newPath, json_encode($data));
        unlink($oldPath);
    }

    public function deleteProfile(string $username): void {
        $username = $this->clearUpUsername($username);
        $path = $this->pathForUsername($username);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function createProfile(string $username, string $email): void {
        $username = $this->clearUpUsername($username);
        if ($username === null) {
            return;
        }
        $path = $this->pathForUsername($username);
        if (file_exists($path)) {
            return;
        }
        $data = [
            'username' => $username,
            'email' => $email,
            'creationDate' => date("Y-m-d H:i:s")
        ];
        file_put_contents($path, json_encode($data));
    }







}