<?php

interface UserDAO {

    public function getByUsername(string $username): ?UserData;

    public function search(string $query): array;

    public function updateProfile(string $username, array $data): void;

    public function renameProfile(string $oldUsername, string $newUsername): void;

    public function deleteProfile(string $username): void;

    public function createProfile(string $username, string $email): void;
}