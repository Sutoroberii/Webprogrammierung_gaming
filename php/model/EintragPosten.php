<?php
class EintragPosten{
    private $id;
    private $text;
    private $datum; 
    private $game; 
    private $autor; 
    private $bild; 
    private $userId;



    public function __construct($id, $text, $datum, $game, $autor, $bild, $userId){
        $this->id = $id;
        $this->autor = $autor;
        $this->datum = $datum;
        $this->game = $game;
        $this->bild = $bild;
        $this->text = $text;
        $this->userId = $userId;  
    }

        public function getId(){
        return $this->id;
    }

    public function getText(){
        return $this->text;
    }

    public function getDate(){
        return $this->datum;
    }

    public function getGame(){
        return $this->game;
    }

    public function getAuthor(){
        return $this->autor;
    }

     public function getImg(){
        return $this->bild;
    }

    public function getUserId(){
        return $this->userId;
    }

    public function update($text, $datum, $game, $autor, $bild){
        $this->text = $text;
        $this->datum = $datum;
        $this->game = $game;
        $this->autor = $autor;
        $this->bild = $bild;
    }
}