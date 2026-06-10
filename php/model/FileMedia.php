<?php
require_once __DIR__ . "/MediaDao.php";

class FileMedia implements MediaDao {

    private string $uploadFilePath;
    private string $publishFilePath;

    public function __construct(string $uploadFilePath, string $publishFilePath) {
        $this->uploadFilePath = $uploadFilePath;
        $this->publishFilePath = $publishFilePath;
    }

    private function checkValidMedia(array $media): ?string {
        $validMediaTypes = ["media/jpeg","media/png"];
        if (!in_array($media["type"], $validMediaTypes)) {
            return "Only jpeg and png images are allowed";
        }
        if ($media["size"] > 1 *1024*1024) {
            return "Image must be smaller than 1MiB";
        }
        return null;
    }

    private function getFileExtension(string $type): string {
        return match ($type) {"media/jpeg"=> "jpg","media/png"=> "png", default => "bin"};
    }

    public function saveMedia(string $username, ?string $associatedId, array $mediaData): string {
        $error = $this->checkValidMedia($mediaData);
        if ($error !== null) {
            throw new Exception($error);
        }
        $fileExtension = $this->getFileExtension($mediaData["type"]);
        $fileName = "media-" .time() . "." . $fileExtension;
        $uploadPathForUser = $this->uploadFilePath . "/" . $username;

        if (!is_dir($uploadPathForUser)) {
            mkdir($uploadPathForUser,0755, true);
        }
        file_put_contents($uploadPathForUser ."/". $fileName, base64_decode($mediaData["data"]));
        return $this->publishFilePath . "/" . $username . "/" . $fileName;
    }
    
}

