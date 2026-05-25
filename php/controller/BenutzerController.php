<?php

use Random\IntervalBoundary;

if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/model/BenutzerEintrag.php";
require_once $abs_path . "/php/model/Benutzer.php";

class BenutzerController {
    public function createUser() {
        $this->checkUserParam();

        if(!$this->checkUserRequiredParam()) {
            header("Location:index.php"); 
            exit;
        }

        if (!$this->checkMatchingPasswords($_POST["passwort"], $_POST["passwortwiederholung"])) {
            header("Location: registrieren.php");
            exit;
        }

        try{
            $Benutzer = Benutzer::getInstance(); 
            $hashedPasswort = password_hash($_POST["passwort"], PASSWORD_DEFAULT);
            $Benutzer->createUser($_POST["benutzername"], $_POST["email"], $hashedPasswort);
            $_SESSION["message"] = "new_user"; 
        } catch (UserAlreadyExistException $exc) {
            $this->handleUserAlreadyExistException();
        } catch(InvalidInputException $exc) {
            $this->handleInvalidInputException();
        }

    }

    public function readUser() {
        if (!isset($_SESSION["loggedInUserId"])){
            $this->handleMissingUserIDException();
        }

        try{
            $id = intval($_SESSION["loggedInUserId"]);
            $Benutzer = Benutzer::getInstance();
            $user = $Benutzer->readUser($id);
            return $user;
        }catch(MissingUserIDException $exc){
            $this->handleMissingUserIDException();
        }
    }

    public function updateUser(){
        $this->checkId(); 
        if (!isset($_POST['loggedInUserId'])) {
            $this->handleMissingUserIDException();
        }

        $id = intval($_POST['loggedInUserId']); 

        if (empty($_POST['benutzername']) || empty($_POST['email'])) {
            $this->handleInvalidInputException();
        }

        $dao = Benutzer::getInstance();
        $current = $dao->readUser($id);
        if (!empty($_POST['passwort'])) {
            $pwdHash = password_hash($_POST['passwort'], PASSWORD_DEFAULT);
        } else {
            $pwdHash = $current->getPasswort();
        }

        $updatedUser = $dao->updateUser(
            $id,
            $_POST['benutzername'],
            $_POST['email'],
            $pwdHash
        );

        //hier kommt noch was zu Kommentaren hin 

    }

    public function deleteUser() {
        $this->checkId();

        try {
            $Benutzer = Benutzer::getInstance(); 
            $Benutzer->deleteUser($_GET["loggedInUserId"]);
            $_SESSION["message"] = "delete_user"; 
        } catch (MissingUserIDException $exc) {
            $this->handleDataAccessException();
        }
    }

    public function loginUser() {
        $this->checkLoginParam();

        try {
            $Benutzer = Benutzer::getInstance(); 
            $user = $Benutzer->loginUser($_POST["email"], $_POST["passwort"]);
            $_SESSION["message"] = "login_user";
            return $user;
        } catch (InvalidInputException $exc) {
            $this->handleInvalidInputException();
        } catch (UserAlreadyLoggedInException $exc) {
            $this->handleUserAlreadyLoggedInException();
        } catch(MissingUserIDException $exc) {
            $this->handleMissingUserIDException(); 
        }
    }

    public function logoutUser() {
        $Benutzer = Benutzer::getInstance();
        $Benutzer->logoutUser();
        $_SESSION["message"] = "logout_user";
        
    }

    public function updateShowBenutzer() {
        $this->checkId(); 
        try {
            $Benutzer = Benutzer::getInstance(); 
            $user = $Benutzer->readUser($_SESSION["loggedInUserId"]);

            $_SESSION["loggedInUserId"] = $user->getId();
            $_SESSION["benutzername"] = $user->getBebenutzername();
            $_SESSION["email"] = $user->getEmail();
        } catch (MissingUserIDException $exc) {
            $this->handleMissingUserIDException();
        } catch (UserAlreadyExistException $exc) {
            $this->handleUserAlreadyExistException();
        }
    }

    public function findOrCreateByEmail(string $email) {
        try {
            $dao = Benutzer::getInstance(); 
            $userEntry = $dao->readByEmail($email); 
        } catch (MissingUserIDException $e) {
            $this->handleMissingUserIDException();
        }
        return $userEntry;
    }

     private function checkId() {
        if (!isset($_REQUEST["loggedInUserId"]) || !is_numeric($_REQUEST["loggedInUserId"])) {
            $this->handleMissingUserIDException();
        }
    }

    private function checkUserParam() {
        if (!isset($_POST["passwort"]) || !isset($_POST["email"]) || !isset($_POST["benutzername"])) {
            $_SESSION["message"] = "missing_parameters";
            header("Location: registrieren.php");
            exit;
        }
    }

  private function checkUserRequiredParam() {
        if (empty($_POST["email"]) || empty($_POST["passwort"]) || empty($_POST["benutzername"])) {
            $_SESSION["message"] = "missing_required_parameters";
            foreach (["benutzername", "email"] as $field) {
                $_SESSION[$field] = $_POST[$field];
            }
            return false;
        } else {
            return true;
        }
    }

    private function checkLoginParam(){
        if(!isset($_POST["email"]) || !isset($_POST["passwort"])){
            $_SESSION["message"] = "missing_parameters";
            header("Location: index.php");
            exit;
        }
    }

    private function handleMissingUserIDException() {
        $_SESSION["message"] = "invalid_user_id";
        header("Location: index.php");
        exit;
    }

    private function handleUserAlreadyExistException() {
        $_SESSION["message"] = "user__already_exist_error";
        header("Location: index.php");
        exit;
    }

    private function handleInvalidInputException() {
        $_SESSION["message"] = "invalid_input";
        header("Location: anmelden.php");
        exit;
    }

    private function handleUserAlreadyLoggedInException() {
        $_SESSION["message"] = "user_already_logged_in";
        header("Location: index.php");
        exit;
    }

    private function checkMatchingPasswords(string $passwort, string $passwortwiederholung): bool {
        if ($passwort !== $passwortwiederholung) {
            $_SESSION["message"] = "passwort_mismatch";
            return false;
        }
        return true;
    }
}
