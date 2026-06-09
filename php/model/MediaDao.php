<?php

interface MediaDao {
    public function saveMedia(string $username, ?string $associatedId, array $mediaData): string;
}