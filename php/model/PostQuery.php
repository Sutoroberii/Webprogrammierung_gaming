<?php

class PostQuery {

    //** Search Parameters */
    private ?string $wordsearch = null;
    private ?string $tagsearch = null;
    private ?string $authorsearch = null;
    private string $sort = 'newest';
    private int $page = 1;
    private int $limit = 10;

    public static function create(): self {
        return new PostQuery();
    }

    public function usingWordsearch(string $wordsearch): self {
        $this->wordsearch = $wordsearch;
        return $this;
    }

    public function usingTagsearch(string $tagsearch): self {
        $this->tagsearch = $tagsearch;
        return $this;
    }

    public function usingAuthorsearch(string $authorsearch): self {
        $this->authorsearch = $authorsearch;
        return $this;
    }

    public function usingSort(string $sort): self {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Sets the displayed page number
     * @param int $page
     * @return self
     */
    public function usingPage(int $page): self {
        $this->page = max(1, $page);
        return $this;
    }

    /**
     * Sets the number of posts to display per page
     * @param int $limit
     * @return self
     */
    public function usingLimit(int $limit): self {
        $this->limit = max(1, $limit);
        return $this;
    }

    public function getWordsearch(): ?string {
        return $this->wordsearch;
    }
    public function getTagsearch(): ?string {
        return $this->tagsearch;
    }
    public function getAuthorsearch(): ?string {
        return $this->authorsearch;
    }
    public function getSort(): string {
        return $this->sort;
    }
    public function getPage(): int {
        return $this->page;
    }
    public function getLimit(): int {
        return $this->limit;
    }


}