<?php

if(!isset($abs_path)){
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/model/Eintrag.php";
require_once $abs_path . "/php/model/EintragPosten.php";

class BeitragController {

    public function createNewEntry(){
            $this->requireLogin();
            $this->checkEntryParam();

            if(!$this->checkEntryRequiredParam()){
                header("Location: beitrag-neu.php");
                exit;
            }

            $bildneu = null;

            
        if (isset($_FILES["bild"]) && is_uploaded_file($_FILES["bild"]["tmp_name"])) {
            $uploadFile = "images/buecher/";

            $bildneu = $uploadFile . basename($_FILES["bild"]["name"]);

            if (!move_uploaded_file($_FILES["bild"]["tmp_name"], $bildneu)) {
                $_SESSION["message"] = "upload_error";  
            }
        }else {
            $bildneu = $_SESSION["bild"] ?? "";
        }

        try{
            $buechereintrag = Buechereintrag::getInstance();
            $buechereintrag->createNewEntry($_POST["autor"],$_POST["datum"],$_POST["game"],$bildneu,$_POST["text"], $_SESSION["loggedInUserId"]);
            $_SESSION["message"] = "new_entry";
        }catch(InternalErrorException$exc){
            $this->handleInternalErrorException();
        }
    }

        public function readEntry(){
        $this->checkId();

        try{
            $id = intval($_GET["id"]);

            $buechereintrag = Buechereintrag::getInstance();
            $entry = $buechereintrag->readEntry($id);

            return $entry;
        }catch(MissingEntryException $exc){
            $this->handleMissingEntryException();
        }
    }

        public function updateEntry() {
        $this->requireLogin();
        $this->checkId();
        $this->checkEntryParam();

        if (!$this->checkEntryRequiredParam()) {
            $_SESSION["id"] = $_POST["id"];
            $encID = urlencode($_POST["id"]);
            header("Location: bucheintrag-aendern-anzeige.php?id=$encID");
            exit;
        }

        $bildneu = null;
        if (isset($_FILES["bild"]) && is_uploaded_file($_FILES["bild"]["tmp_name"])) {
            $uploadFile = "images/Beitragsbilder/";
            if (!is_dir($uploadFile)) {
                mkdir($uploadFile, 0755, true);
            }

            $bildneu = $uploadFile . basename($_FILES["bild"]["name"]);

            if (!move_uploaded_file($_FILES["bild"]["tmp_name"], $bildneu)) {
                $_SESSION["message"] = "upload_error";
                
            }
        } else {
            $bildneu = $_SESSION["bild"] ?? "";
        }

        try {
            $buechereintrag = Buechereintrag::getInstance();
            $entry = $buechereintrag->updateEntry(
                $_POST["id"],
                $_POST["autor"],
                $_POST["datum"],
                $_POST["game"],
                $bildneu,
                $_POST["text"],
                $_SESSION["loggedInUserId"]
            );
            $_SESSION["message"] = "update_entry";
            return $entry;
        } catch (MissingEntryException $exc) {
            $this->handleMissingEntryException();
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        }
    }


    
      public function deleteEntry(){
        $this->requireLogin();
        $this->checkId();

        try {
            $buechereintrag = Buechereintrag::getInstance();
            $buechereintrag->deleteEntry($_GET["id"], $_SESSION["loggedInUserId"]);
            $_SESSION["message"] = "delete_entry";
        } catch (MissingEntryException $exc) {
            $this->handleMissingEntryException();
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        }
    }

        public function updateShowEntry(){
        $this->requireLogin();
        $this->checkId();

        try {
            $buechereintrag = Buechereintrag::getInstance();
            $entry =$buechereintrag->readEntry($_GET["id"]);
          
            $_SESSION["id"] = $entry->getId();
            $_SESSION["datum"] = $entry->getDate();
            $_SESSION["text"] = $entry->getText();
            $_SESSION["game"] = $entry->getGame();
            $_SESSION["autor"] = $entry->getAuthor();
            $_SESSION["bild"] = $entry->getImg();
        } catch (MissingEntryException $exc) {
            $this->handleMissingEntryException();
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        }
    }

        private function checkId() {
        if (!isset($_REQUEST["id"]) || !ctype_digit($_REQUEST["id"])) {
            $this->handleMissingEntryException();
        }
    }

        private function checkEntryParam() {
        if (!isset($_POST["text"]) || !isset($_POST["datum"]) || !isset($_POST["game"]) || !isset($_POST["autor"]) || !isset($_FILES["bild"]) || !isset($_POST["submit"])) {
            $_SESSION["message"] = "missing_parameters";
            header("Location:index.php");
            exit;
        }
    }

        private function checkEntryRequiredParam() {
        if (empty($_POST["autor"]) ||empty($_POST["datum"]) ||empty($_POST["text"]) ||empty($_POST["game"]) ) {
            $_SESSION["message"] = "missing_required_parameters";
            foreach (["text", "datum", "game", "autor"] as $field) {
                $_SESSION[$field] = $_POST[$field];
            }
            return false;
        }
        return true;
    }

    private function handleMissingEntryException() {
        $_SESSION["message"] = "invalid_entry_id";
        header("Location: index.php");
        exit;
    }

        private function handleInternalErrorException(){
        $_SESSION["message"] = "internal_error";
        header("Location: index.php");
        exit;
    }

    private function requireLogin(){
        if(!isset($_SESSION["loggedInUserId"])){
            $_SESSION["message"] = "login_required";
            header("Location: anmelden.php");
            exit;
        }
    }


       public function searchByTerm($query) {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        try {
            $buechereintrag = Buechereintrag::getInstance();
            return $buechereintrag->searchByTerm($query);
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        }
    }




        
    

}
