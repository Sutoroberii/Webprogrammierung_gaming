<?php

class PostQueryResult {

    public function __construct(private array $posts, private int $totalResults, private PostQuery $query) {   
    }

    public function getResults(): array {
        return $this->posts;
    }

    public function getTotalResults(): int {
        return $this->totalResults;
    }

    public function getCurrentPage(): int {
        return $this->query->getPage();
    }

    public function getLimit(): int {
        return $this->query->getLimit();
    }
    public function getTotalPages(): int {
        return ceil($this->totalResults / $this->getLimit());
    }
    
}