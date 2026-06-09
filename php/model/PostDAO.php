<?php

require_once __DIR__ ."/PostQuery.php";
require_once __DIR__ ."/PostQueryResult.php";

interface PostDAO {

    public function createPost(PostData $post): PostData;

    public function deletePost(int $id): bool;

    public function updatePost(PostData $post): bool;

    public function findByUrl(string $url): ?PostData;

    public function findbyId(int $id): ?PostData;

    public function findAll(): array;

    public function query(PostQuery $query): PostQueryResult;

    public function getBestTags(int $limit = 10): array;

}