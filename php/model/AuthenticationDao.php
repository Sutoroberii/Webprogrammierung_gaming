<?php

interface AuthenticationDao {

    public function createUser(string $username, string $email, string $passwordHash): void;

    public function deleteUser(string $username): void;

    public function getUserByUsername(string $username): ?AuthenticationUserData;

    public function getUserByEmail(string $email): ?AuthenticationUserData;

    public function usernameAlreadyTaken(string $username): bool;

    public function emailAlreadyTaken(string $email): bool;

    public function updateUsername(string $oldUsername, string $newUsername): void;

    public function updateEmail(string $username, string $email): void;

    public function updatePassword(string $username, string $newHash): void;

}