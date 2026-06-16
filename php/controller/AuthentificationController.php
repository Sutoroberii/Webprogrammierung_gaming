<?php

require_once __DIR__ . "/../model/Authentication.php";
require_once __DIR__ . "/../model/AuthenticationDao.php";
require_once __DIR__ . "/../model/User.php";
require_once __DIR__ . "/../model/SessionControl.php";

class AuthenticationController
{
    private AuthenticationDao $authDao;
    private UserDao $userDao;
    private SessionControl $sessionControl;

    public function __construct(SessionControl $sessionControl)
    {
        $this->sessionControl = $sessionControl;
        $this->authDao = Authentication::getInstance();
        $this->userDao = User::getInstance();
    }

    public function validateRegister(
        string $username,
        string $email,
        string $password,
        string $confirmPassword
    ): array {

        $username = trim($username);
        $email = trim($email);

        $errors = [];

        $this->validateUsername($username, $errors);
        $this->validateEmail($email, $errors);
        $this->validatePassword($password, $confirmPassword, $errors);

        return $errors;
    }

    public function login(string $username, string $password): array
    {
        $username = trim($username);

        if ($username === '') {
            return [
                'success' => false,
                'error' => 'Name ist nötig'
            ];
        }

        if ($password === '') {
            return [
                'success' => false,
                'error' => 'Passwort ist nötig'
            ];
        }

        $user = $this->authDao->getUserByUsername($username);

        if ($user === null) {
            return [
                'success' => false,
                'error' => 'Name oder Passwort ist falsch'
            ];
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return [
                'success' => false,
                'error' => 'Name oder Passwort ist falsch'
            ];
        }

        $this->sessionControl->loginUser($user->getUsername());

        $_SESSION['username'] = $user->getUsername();

        return ['success' => true];
    }

    public function logout(): void
    {
        $this->sessionControl->logoutUser();
    }

    public function register(
        string $username,
        string $email,
        string $password,
        string $confirmPassword
    ): array {

        $username = trim($username);
        $email = trim($email);

        $errors = $this->validateRegister(
            $username,
            $email,
            $password,
            $confirmPassword
        );

        if (!empty($errors)) {
            return [
                'success' => false,
                'error' => implode("\n", $errors)
            ];
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_BCRYPT
        );

        $this->authDao->createUser(
            $username,
            $email,
            $passwordHash
        );

        $this->userDao->createProfile(
            $username,
            $email
        );

        $this->sessionControl->loginUser($username);

        $_SESSION['username'] = $username;

        return ['success' => true];
    }

    private function validateUsername(
        string $username,
        array &$errors
    ): void {

        if ($username === '') {
            $errors[] = 'Name ist nötig';
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9-]{1,10}$/', $username)) {
            $errors[] =
                'Name darf nur Zahlen, Buchstaben und Bindestriche enthalten';
        }

        if ($this->authDao->usernameAlreadyTaken($username)) {
            $errors[] = 'Benutzername ist bereits vergeben';
        }
    }

    private function validateEmail(
        string $email,
        array &$errors
    ): void {

        if ($email === '') {
            $errors[] = 'E-Mail ist nötig';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-Mail ist ungültig';
        }

        if ($this->authDao->emailAlreadyTaken($email)) {
            $errors[] = 'E-Mail ist bereits vergeben';
        }
    }

    private function validatePassword(
        string $password,
        string $confirmPassword,
        array &$errors
    ): void {

        if ($password === '') {
            $errors[] = 'Passwort ist nötig';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwörter stimmen nicht überein';
        }
    }

    public function checkUsernameAvailability(string $username): array
    {
    $username = trim($username);
    $errors = [];

    $this->validateUsername($username, $errors);

    if (!empty($errors)) {
        return [
            "available" => false,
            "message" => $errors[0]
        ];
    }

    return [
        "available" => true,
        "message" => ""
    ];
    }
}