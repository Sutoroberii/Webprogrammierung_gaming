<?php 
if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/model/BenutzerEintrag.php";
require_once $abs_path . "/php/model/BenutzerDAO.php";


class BenutzerSession implements BenutzerDAO {
    private static $instance = null; 

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new BenutzerSession();
        }
        return self::$instance;
    }

    private $benutzer = array();

    private function __construct()
    {
        if(isset($_SESSION["user"])) {
            $this->benutzer = unserialize($_SESSION["user"]);
        } else {
            $this->benutzer[0] = new BenutzerEintrag(0, "sutoroberi", "maren.schaa@uni-oldenburg.de", password_hash("test123", PASSWORD_DEFAULT));
            $_SESSION["user"] = serialize($this->benutzer); 
            $_SESSION["nextUserId"] = 1;
        }
    }

    #[Override]
    public function readByEmail($email)
    {
        foreach($this->benutzer as $benutzer) {
            if($benutzer->getEmail() == $email) {
                return $benutzer;
            }
        }
        throw new MissingUserIDException();
    }


    public function createUser($benutzername, $email, $passwort) {
        $hashedPasswort = password_hash($passwort, PASSWORD_DEFAULT); 
        $this->benutzer[$_SESSION["nextUserId"]] = new BenutzerEintrag($_SESSION["nextUserId"], $benutzername, $email, $hashedPasswort); 
        $_SESSION["nextUserID"] = $_SESSION["nextUserId"] + 1; 
        $_SESSION["user"] = serialize($this->benutzer);
    }

    public function readUser($id){
        foreach($this->benutzer as $benutzer) {
            if($benutzer->getId() == $id){
                return $benutzer;
            }
        }

        throw new MissingUserIDException();
    }


    public function readBenutzername($benutzername) {
        foreach($this->benutzer as $benutzer){
            if($benutzer->getBenutzername() == $benutzername) {
                return $benutzer;
            }
        }
        throw new MissingUserIDException();
    }

    public function updateUser($id, $benutzername, $email, $passwort) {
        foreach($this->benutzer as $benutzer){
            if($benutzer->getID() == $id){
                $benutzer->update($id, $benutzername, $email, $passwort); 
                $_SESSION["user"] = serialize($this->benutzer);
                return $benutzer;
            }
        }
        throw new UserAlreadyExistException();
    }

    public function deleteUser($id) {
        foreach($this->benutzer as $key => $benutzer) {
            if($benutzer->getID() == $id){
                unset($this->benutzer[$key]);
                $this->benutzer = array_values($this->benutzer); 
                $_SESSION["user"] = serialize($this->benutzer); 
                unset($_SESSION["loggedInUserId"]);
                return;
            }
        }
    }

    public function loginUser($email, $passwort) {
        foreach($this->benutzer as $benutzer) {
            if($benutzer->getEmail() == $email && password_verify($passwort, $benutzer->getPasswort())) {
                $_SESSION["loggedInUserId"] = $benutzer -> getID();
                return $benutzer;
            }
        }
        throw new InvalidInputException();
    }

    public function logoutUser() {
        unset($_SESSION["loggedInUserId"]);
    }

    public function getUsers(){
        return $this->benutzer; 
    }
}

?>