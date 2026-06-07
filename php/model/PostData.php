<?php

class PostData {

    public function __construct(private string $postTitle, private array $postTags, private ?string $postMedia, private string $postText, private string $postUrl, private ?int $postId = null, private ?string $postAuthor = null, private ?int $postDate = null) {
    }

    public function getPostTitle(): string {
        return $this->postTitle;
    }

    public function getPostTags(): array {
        return $this->postTags;
    }

    public function getPostMedia(): ?string {
        return $this->postMedia;
    }

    public function getPostText(): ?string {
        return $this->postText;
    }

    public function getPostUrl(): ?string {
        return($this->postUrl);
    }

    public function getPostId(): ?int {
        return($this->postId);
    }

    public function getPostAuthor(): ?string {
        return $this->postAuthor;
    }
    public function getPostDate(): ?string {
        return $this->postDate;
    }


}