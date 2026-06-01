<?php 
if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/model/Eintrag.php";
require_once $abs_path . "/php/model/EintragDAO.php";


class EintragSession implements EintragDAO {

    private static $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EintragSession();
        }
        return self::$instance;
    }

    private $entries = array();

        private function __construct(){
        if(isset($_SESSION["entries"])){
            $this->entries = unserialize($_SESSION["entries"]);
        }else{
            $this->entries[0] = new EintragPosten(0,"Test1231231","20.05.2025","Horror","Christina","Bild",0);
            $this->entries[1] = new EintragPosten(1,"Test1262262","16.05.2025","Science-Fiction","Christian","Bild",0);
            $this->entries[2] = new EintragPosten(2,"Test12331","24.05.2025","Krimi","Max","Bild",0);
            $this->entries[3] = new EintragPosten(3,"Test12312bbbb31","20.05.2025","Horror","Christina","Bild",0);
            $this->entries[4] = new EintragPosten(4,"Test126226dfdf2","11.05.2025","Fiction","Christian","Bild",0);
            $this->entries[5] = new EintragPosten(5,"Test1233sdfds1","27.05.2025","Romance","Max","Bild",0);
            $_SESSION["entries"] = serialize($this->entries);
            $_SESSION["nextId"] = 6;
        }
    }


       public function readEntry($id){
        foreach($this->entries as $entry){
            if($entry->getId() == $id){
                return $entry;
            }
        }
        throw new MissingEntryException();
    }

        public function createNewEntry( $autor, $datum, $game, $bild, $text, $userId){
        $this->entries[$_SESSION["nextId"]] = new EintragPosten($_SESSION["nextId"], $text, $datum, $game, $autor, $bild, $userId);
        $_SESSION["nextId"]=$_SESSION["nextId"]+1;
        $_SESSION["entries"] = serialize($this->entries);
    }


        public function updateEntry($id,  $autor, $datum, $game, $bild, $text, $userId){
        foreach($this->entries as $entry){
            if($entry->getId() == $id){
                if($entry->getUserId() != $userId){
                    throw new InternalErrorException();
                }
                $entry->update( $text, $datum, $game, $autor, $bild);
                $_SESSION["entries"] = serialize($this->entries);
                return $entry;
            }
        }
        throw new MissingEntryException();
    }

        public function deleteEntry($id, $userId){
        foreach($this->entries as $key => $entry){
            if($entry->getId() == $id){
                if($entry->getUserId() != $userId){
                    throw new InternalErrorException();
                }
                unset($this->entries[$key]);
                $this->entries = array_values($this->entries);
                $_SESSION["entries"] = serialize($this->entries);
                return;
            }
        }
    }

    public function getEntries(){
        return $this->entries;
    }

    public function getEntriesByUserId($userId){
        $userEntries = array();
        foreach($this->entries as $entry){
            if($entry->getUserId() == $userId){
                $userEntries[] = $entry;
            }
    }
        return $userEntries;
    }

    public function searchByTerm($query) {
        $query = mb_strtolower($query);
        $results = [];

        foreach ($this->entries as $entry) {
            if (
                strpos(mb_strtolower($entry->getAuthor()), $query) !== false ||
                strpos(mb_strtolower($entry->getGame()), $query) !== false ||
                strpos(mb_strtolower($entry->getText()), $query) !== false
                ) {
                $results[] = $entry;
            }
        }
        return $results;
    }

}
