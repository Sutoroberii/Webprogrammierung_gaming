<?php

require_once __DIR__ . "/MediaDao.php";

class FileMedia implements MediaDao {
    private string $uploadFilePath;
    private string $publishFilePath;

    public function __construct(string $uploadFilePath, string $publishFilePath) {
        $this->uploadFilePath = rtrim($uploadFilePath, "/");
        $this->publishFilePath = rtrim($publishFilePath, "/");
    }

    private function checkValidMedia(array $media): ?string {
        $validMediaTypes = ["image/jpeg", "image/png"];

        if (!isset($media["type"]) || !in_array($media["type"], $validMediaTypes, true)) {
            return "Only jpeg and png images are allowed";
        }

        if (!isset($media["size"]) || $media["size"] > 1024 * 1024) {
            return "Image must be smaller than 1MiB";
        }

        return null;
    }

    private function getFileExtension(string $type): string {
        return match ($type) {
            "image/jpeg" => "jpg",
            "image/png" => "png",
            default => "bin"
        };
    }

    public function saveMedia(string $username, ?string $associatedId, array $mediaData): string {
        $error = $this->checkValidMedia($mediaData);

        if ($error !== null) {
            throw new Exception($error);
        }

        $fileExtension = $this->getFileExtension($mediaData["type"]);
        $safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
        $fileName = "media-" . time() . "-" . bin2hex(random_bytes(4)) . "." . $fileExtension;
        $uploadPathForUser = $this->uploadFilePath . "/" . $safeUser;

        if (!is_dir($uploadPathForUser) && !mkdir($uploadPathForUser, 0755, true) && !is_dir($uploadPathForUser)) {
            throw new RuntimeException("Upload-Ordner konnte nicht erstellt werden.");
        }

        $binaryData = base64_decode($mediaData["data"], true);

        if ($binaryData === false) {
            throw new RuntimeException("Bilddaten konnten nicht gelesen werden.");
        }

        file_put_contents($uploadPathForUser . "/" . $fileName, $binaryData);

        return $this->publishFilePath . "/" . $safeUser . "/" . $fileName;
    }
}