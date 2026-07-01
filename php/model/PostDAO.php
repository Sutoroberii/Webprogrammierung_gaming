<?php

require_once __DIR__ ."/PostQuery.php";
require_once __DIR__ ."/PostQueryResult.php";

interface PostDAO
{
    public function createPost(PostData $post): PostData;

    public function findById(int $id): ?PostData;

    public function findByUrl(string $url): ?PostData;

    public function findAll(): array;

    public function updatePost(PostData $post): bool;

    public function deletePost(int $id): bool;

    public function deletePostByAuthor(int $id, string $author): bool;
}