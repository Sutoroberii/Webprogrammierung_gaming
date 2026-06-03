<?php 
class BenutzerEintrag {
    private $id; 
    private $benutzername; 
    private $email;
    private $passwort;

    public function __construct($id, $benutzername, $email, $passwort)
    {
        $this->id = $id; 
        $this->benutzername = $benutzername; 
        $this->email = $email;
        $this->passwort = $passwort;
    }

    public function getId(){
        return $this->id;
    }

    public function getBenutzername(){
        return $this->benutzername; 
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPasswort(){
        return $this->passwort;
    }

    public function update($id, $benutzername, $email, $passwort){
        $this->id= $id; 
        $this->benutzername = $benutzername; 
        $this->email = $email;
        $this->passwort = $passwort;
    }

}