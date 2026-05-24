<?php

class InternalErrorException extends Exception{}

class MissingUserIDException extends Exception{}

class UserAlreadyExistException extends Exception{}

class UserAlreadyLoggedInException extends Exception{}

class InvalidInputException extends Exception{}

interface BenutzerDAO {
    public function createUser($benutzername, $email, $passwort);

    public function readUser($id);  

    public function deleteUser($id);

    public function loginUser($email, $passwort);

    public function logoutUser(); 

    public function readByEmail($email);
}