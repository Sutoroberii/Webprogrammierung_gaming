<?php

require_once __DIR__ . "/PostQuery.php";
require_once __DIR__ . "/PostQueryResult.php";
require_once __DIR__ . "/PostDao.php";
require_once __DIR__ . "/PostData.php";
require_once __DIR__ . "/../parser/MetadataParser.php";

class FilePost implements PostDAO
{
    private string $filePath;
    private MetadataParser $parser;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->parser = new MetadataParser();
    }

    public function createPost(PostData $post): PostData
    {
        $postId = $this->generateUniquePostId();

        $slug = $this->generateURLfriendly($post->getPostTitle());
        $fullUrl = $postId . "-" . $slug;

        $metadata = [
            "postId" => $postId,
            "postTitle" => $post->getPostTitle(),
            "postTags" => $post->getPostTags(),
            "postUrl" => $fullUrl,
            "postAuthor" => $post->getPostAuthor()
        ];

        if ($post->getPostMedia() !== null) {
            $metadata["postMedia"] = $post->getPostMedia();
        }

        $content = $this->buildMarkdownFile(
            $metadata,
            $post->getPostText()
        );

        file_put_contents(
            $this->getPostPath($postId),
            $content
        );

        return new PostData(
            postId: $postId,
            postTitle: $post->getPostTitle(),
            postTags: $post->getPostTags(),
            postMedia: $post->getPostMedia(),
            postText: $post->getPostText(),
            postUrl: $fullUrl,
            postAuthor: $post->getPostAuthor(),
            postDate: time()
        );
    }

    public function deletePost(int $id): bool
    {
        $path = $this->getPostPath($id);

        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
    }

    public function updatePost(PostData $post): bool
    {
        $postId = $post->getPostId();

        if ($postId === null) {
            return false;
        }

        $path = $this->getPostPath($postId);

        if (!file_exists($path)) {
            return false;
        }

        $slug = $postId . "-" . $this->generateURLfriendly($post->getPostTitle());

        $metadata = [
            "postId" => $postId,
            "postTitle" => $post->getPostTitle(),
            "postTags" => $post->getPostTags(),
            "postUrl" => $slug,
            "postAuthor" => $post->getPostAuthor()
        ];

        if ($post->getPostMedia() !== null) {
            $metadata["postMedia"] = $post->getPostMedia();
        }

        $content = $this->buildMarkdownFile(
            $metadata,
            $post->getPostText()
        );

        file_put_contents($path, $content);

        return true;
    }

    public function findbyId(int $id): ?PostData
    {
        return $this->loadPostFromFile(
            $this->getPostPath($id)
        );
    }

    public function findByUrl(string $url): ?PostData
    {
        if (preg_match('/^(\d+)/', $url, $matches)) {
            return $this->findbyId((int) $matches[1]);
        }

        if (is_numeric($url)) {
            return $this->findbyId((int) $url);
        }

        return null;
    }

    public function findAll(): array
    {
        $posts = [];

        foreach (glob($this->filePath . "/*.md") as $file) {
            $post = $this->loadPostFromFile($file);

            if ($post !== null) {
                $posts[] = $post;
            }
        }

        usort(
            $posts,
            fn(PostData $a, PostData $b)
            => ($b->getPostDate() ?? 0)
            - ($a->getPostDate() ?? 0)
        );

        return $posts;
    }

    public function query(PostQuery $query): PostQueryResult
    {
        $posts = [];

        foreach (glob($this->filePath . "/*.md") as $file) {

            $post = $this->loadPostFromFile($file);

            if ($post === null) {
                continue;
            }

            if (
                !$this->matchesAuthor(
                    $post,
                    $query->getAuthorSearch()
                )
            ) {
                continue;
            }

            $fileContent = file_get_contents($file);
            $metadata = $this->parser->parse($fileContent);
            $postBody = $this->parser->removeMetadata($fileContent);

            if (
                !$this->matchesTagSearch(
                    $metadata,
                    $query->getTagSearch()
                )
            ) {
                continue;
            }

            if (
                !$this->matchesWordSearch(
                    $query->getWordSearch(),
                    $metadata,
                    $postBody
                )
            ) {
                continue;
            }

            $posts[] = $post;
        }

        usort(
            $posts,
            fn(PostData $a, PostData $b)
            => ($b->getPostDate() ?? 0)
            - ($a->getPostDate() ?? 0)
        );

        $totalResults = count($posts);

        $postsOnPage = array_slice(
            $posts,
            ($query->getPage() - 1) * $query->getLimit(),
            $query->getLimit()
        );

        return new PostQueryResult(
            $postsOnPage,
            $totalResults,
            $query
        );
    }

    public function getBestTags(int $limit = 10): array
    {
        $tags = [];

        foreach (glob($this->filePath . "/*.md") as $file) {

            $content = file_get_contents($file);
            $metadata = $this->parser->parse($content);

            foreach ($metadata["postTags"] ?? [] as $tag) {
                $tag = strtolower($tag);
                $tags[$tag] = ($tags[$tag] ?? 0) + 1;
            }
        }

        arsort($tags);

        return array_slice(
            array_keys($tags),
            0,
            $limit
        );
    }

    public function loadPostFromFile(string $filePath): ?PostData
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);

        $metadata = $this->parser->parse($content);
        $text = $this->parser->removeMetadata($content);

        $postId = (int) (
            $metadata["postId"]
            ?? basename($filePath, ".md")
        );

        $slug = $metadata["postUrl"]
            ?? $this->generateURLfriendly(
                $metadata["postTitle"]
                ?? (string) $postId
            );

        return new PostData(
            postId: $postId,
            postTitle: $metadata["postTitle"] ?? "",
            postTags: $metadata["postTags"] ?? [],
            postMedia: $metadata["postMedia"] ?? null,
            postText: $text,
            postUrl: $slug,
            postAuthor: $metadata["postAuthor"] ?? null,
            postDate: filemtime($filePath) ?: null
        );
    }

    public function generateURLfriendly(string $title): string
    {
        return preg_replace(
            "/[^a-z0-9]+/",
            "-",
            strtolower(trim($title))
        );
    }

    private function getPostPath(int $postId): string
    {
        return $this->filePath . "/" . $postId . ".md";
    }

    private function generateUniquePostId(): int
    {
        do {
            $postId = random_int(1000, 9000);
        } while (file_exists($this->getPostPath($postId)));

        return $postId;
    }

    private function buildMarkdownFile(array $metadata, string $body): string
    {
        return $this->parser->encode($metadata) . "\n\n" . $body;
    }

    private function matchesAuthor(
        PostData $post,
        ?string $author
    ): bool {

        if ($author === null) {
            return true;
        }

        return $post->getPostAuthor() === $author;
    }

    private function matchesTagSearch(
        array $metadata,
        ?string $tagSearch
    ): bool {

        if ($tagSearch === null) {
            return true;
        }

        $tagSearch = trim($tagSearch);

        if ($tagSearch === "") {
            return true;
        }

        foreach ($metadata["postTags"] ?? [] as $tag) {

            if (
                strtolower($tag)
                === strtolower($tagSearch)
            ) {
                return true;
            }
        }

        return false;
    }

    public function deletePostByAuthor(int $id, string $author): bool
{
    $post = $this->findById($id);

    if ($post === null) {
        return false;
    }

    if ($post->getPostAuthor() !== $author) {
        return false;
    }

    $this->deletePost($id);

    return true;
}

    private function matchesWordSearch(
        ?string $word,
        array $metadata,
        string $postBody
    ): bool {

        if ($word === null) {
            return true;
        }

        $word = trim($word);

        if ($word === "") {
            return true;
        }

        $word = strtolower($word);

        if (stripos($metadata["postTitle"] ?? "", $word) !== false) {
            return true;
        }

        if (stripos($postBody, $word) !== false) {
            return true;
        }

        foreach ($metadata["postTags"] ?? [] as $tag) {
            if (stripos($tag, $word) !== false) {
                return true;
            }
        }

        return false;
    }
}