<?php

require_once __DIR__ ."/PostQuery.php";
require_once __DIR__ ."/PostQueryResult.php";

interface PostDAO {

    public function findbyId(int $id): ?PostData;

    public function findAll(): array;

    public function query(PostQuery $query): PostQueryResult;

    public function getBestTags(int $limit = 10): array;

    public function createPost(PostData $post): PostData;

    public function updatePost(PostData $post): void;

    public function deletePost(int $id): void;

}