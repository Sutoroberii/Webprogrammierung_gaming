<?php
require_once __DIR__ . "/UserDAO.php";
require_once __DIR__ . "/UserData.php";

class FileUser implements UserDAO
{

    private string $filePath;

    // Constructor sets the folder where user JSON files are stored
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    // Clean up a username to remove unwanted characters
    private function clearUpUsername(string $username): string
    {
        return preg_replace('/[^a-z0-9-]/i', '', basename($username));
    }

    // Get the file path for a given username
    private function pathForUsername(string $username): string
    {
        $cleanUsername = $this->clearUpUsername($username);
        return $this->filePath . "/" . $cleanUsername . ".json";
    }

    // Load user data from a JSON file
    public function getByUsername(string $username): ?UserData
    {
        $filePath = $this->pathForUsername($username);

        if (!file_exists($filePath)) {
            return null;
        }

        $data = json_decode(file_get_contents($filePath), true);
        if (!is_array($data)) {
            return null;
        }

        return new UserData(
            $data['username'] ?? $username,
            $data['email'] ?? '',
            $data['creationDate'] ?? ''
        );
    }

    // Search users by username or email
    public function search(string $query): array
    {
        $query = strtolower(trim($query));
        if ($query === '') {
            return [];
        }

        $results = [];
        foreach (glob($this->filePath . "/*.json") as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (!is_array($data)) {
                continue;
            }

            $username = strtolower($data['username'] ?? '');
            $email = strtolower($data['email'] ?? '');

            if (strpos($username, $query) !== false || strpos($email, $query) !== false) {
                $results[] = new UserData(
                    $data['username'] ?? '',
                    $data['email'] ?? '',
                    $data['creationDate'] ?? ''
                );
            }
        }

        return $results;
    }

    // Update an existing user's profile with new data
    public function updateProfile(string $username, array $data): void
    {
        $filePath = $this->pathForUsername($username);

        if (!file_exists($filePath)) {
            return;
        }

        $currentData = json_decode(file_get_contents($filePath), true);
        if (!is_array($currentData)) {
            return;
        }

        $updatedData = array_merge($currentData, $data);
        file_put_contents($filePath, json_encode($updatedData));
    }

    // Rename a user's profile
    public function renameProfile(string $oldUsername, string $newUsername): void
    {
        $oldPath = $this->pathForUsername($oldUsername);
        $newPath = $this->pathForUsername($newUsername);

        if (!file_exists($oldPath) || file_exists($newPath)) {
            return;
        }

        $data = json_decode(file_get_contents($oldPath), true);
        if (!is_array($data)) {
            return;
        }

        $data['username'] = $this->clearUpUsername($newUsername);
        file_put_contents($newPath, json_encode($data));
        unlink($oldPath);
    }

    // Delete a user's profile
    public function deleteProfile(string $username): void
    {
        $filePath = $this->pathForUsername($username);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Create a new user profile
    public function createProfile(string $username, string $email): void
    {
        $filePath = $this->pathForUsername($username);

        if (file_exists($filePath)) {
            return;
        }

        $data = [
            'username' => $this->clearUpUsername($username),
            'email' => $email,
            'creationDate' => date("Y-m-d H:i:s")
        ];

        file_put_contents($filePath, json_encode($data));
    }
}