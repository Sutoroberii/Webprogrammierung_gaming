<?php
require_once __DIR__ ."/../model/Authentication.php";
require_once __DIR__ ."/../model/AuthenticationDao.php";
require_once __DIR__ ."/../model/User.php";
require_once __DIR__ ."/../model/SessionControl.php";

class AuthentificationController {
    private AuthenticationDao $authDao;
    private UserDao $userDao;

    public function __construct(private SessionControl $sessionControl) {
        $this->authDao = Authentication::getInstance();
        $this->userDao = User::getInstance();
    }

    public function validateRegister(string $username, string $email, string $password, string $confirmPassword): array {
        $username = trim($username);
        $email = trim($email);
        $errors = [];
        if ($username == "") {
            $errors[] = 'Name ist nötig';
        }
        if (!preg_match('/^[a-zA-Z0-9-]{1,10}$/', $username)) {
            $errors[] = 'Name darf nur Zahlen, Buchstaben und Bindestriche enthalten';
        }
        if ($this->authDao->usernameAlreadyTaken($username)) {
            $errors[] = 'Name ist bereits vergeben';
        }
        if ($email == "") {
            $errors[] = 'E-Mail ist nötig';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-Mail ist ungültig';
        }
        if ($this->authDao->emailAlreadyTaken($email)) {
            $errors[] = 'E-Mail ist bereits vergeben';
        }
        if ($password == "") {
            $errors[] = 'Passwort ist nötig';
        }
        if ($password != $confirmPassword) {
            $errors[] = 'Passwörter stimmen nicht überein';
        }
        return $errors;
    }

    public function login(string $username, string $password): array {
        $username = trim($username);
        if ($username == "") {
            return ['success' => false, 'error' => 'Name ist nötig'];
        }
        if ($password == "") {
            return ['success' => false, 'error' => 'Passwort ist nötig'];
        }
        $user = $this->authDao->getUserByUsername($username);
        if ($user == null) {
            return ['success'=> false, 'error' => 'Name oder Passwort ist falsch'];
        }
        if (!password_verify($password, $user->getPasswordHash())) {
            return ['success'=> false, 'error' => 'Name oder Passwort ist falsch'];
        }
        $this->sessionControl->loginUser($user->getUsername());
        $SESSION['username'] = $user->getUsername();
        return ['success' => true];
    }

    public function logout(): void {
        $this->sessionControl->logoutUser();
    }

    public function register(string $username, string $email, string $password, string $confirmPassword): array {
        $username = trim($username);
        $email = trim($email);
        $errors = $this->validateRegister($username, $email, $password, $confirmPassword);

        if ($errors !== []) {
            return ['success' => false, 'error' => implode("\n", $errors)];
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->authDao->createUser($username, $email, $hash);
        $this->userDao->createProfile($username, $email);
        $this->sessionControl->loginUser($username);
        $SESSION["username"] = $username;
        return ['success' => true];

    }

}