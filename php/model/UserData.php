<?php

class UserData {

    public function __construct(public string $username, public string $email, public string $creationDate) {
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getCreationDate(): string {
        return $this->creationDate;
    }

}