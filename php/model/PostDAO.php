<?php
class InternalErrorException extends Exception{}
class MissingEntryException extends Exception{}
interface NpcDAO{

    public function createPost();

    public function readPost();

    public function updatePost();

    public function deletePost();

}