<?php
class InternalErrorException extends Exception{}
class MissingEntryException extends Exception{}
interface PostDAO {

    public function createPost($author, $date, $game, $media, $text, $userId);

    public function readPost($postId);

    public function updatePost($postId, $author, $date, $game, $media, $text, $userId);

    public function deletePost($postId);

    public function getPostsFromUser($userId);

    public function searchPosts($query);

}