<?php

class AuthenticationUserData {

    public function __construct(public string $username, public string $email, public string $passwordHash, public string $creationDate) {
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getCreationDate(): string {
        return $this->creationDate;
    }

}