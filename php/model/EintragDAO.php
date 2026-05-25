<?php

class DataAccessException extends Exception{}
class MissingEntryException extends Exception{}

interface EintragDAO{
    public function createNewEntry($autor, $datum, $game, $bild, $text, $userId); 

    public function readEntry($id);

    public function updateEntry($id, $autor, $datum, $game, $bild, $text, $userId);

    public function getEntries();

    public function deleteEntry($id, $userId);

    public function getEntriesByUserId($userID);

    public function searchByTerm($query);
}