<?php
class PostEntry{
    private $author;
    private $date;
    private $game; 
    private $media; 
    private $text; 
    private $userId;
    private $postId;

    public function __construct($author, $date, $game, $media, $text, $userId, $postId){
        $this->author = $author;
        $this->date = $date;
        $this->game = $game;
        $this->media = $media;
        $this->text = $text;
        $this->userId = $userId;
        $this->postId = $postId;
    }

        public function getPostId(){
        return $this->postId;
    }
    public function getAuthor(){
        return $this->author;
    }
    public function getDate(){
        return $this->date;
    }
    public function getGame(){
        return $this->game;
    }
    public function getMedia(){
        return $this->media;
    }
    public function getText(){
        return $this->text;
    }
    public function getUserId(){
        return $this->userId;
    }

    public function update($postId, $author, $date, $game, $media, $text, $userId){
        $this->postId = $postId;
        $this->author = $author;
        $this->date = $date;
        $this->game = $game;
        $this->media = $media;
        $this->text = $text;
        $this->userId = $userId;
    }
}