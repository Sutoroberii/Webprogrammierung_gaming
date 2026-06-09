<?php
require_once __DIR__ ."/PostQuery.php";
require_once __DIR__ ."/PostQueryResult.php";
require_once __DIR__ ."PostDao.php";
require_once __DIR__ ."PostData.php";
require_once __DIR__ ."/../parser/MetadataParser.php";

class FilePost implements PostDao {
    private MetadataParser $parser;

    public function __construct(private string $filePath) {
        $this->parser = new MetadataParser();
    }

    public function createPost(PostData $post): PostData {
        $postId = random_int(1000,9000);
        while (file_exists($this->filePath . "/" . $postId .".md")) {
            $postId = random_int(1000,9000);
        }
        $url = $this->generateURLfriendly($post->getPostTitle());
        $fullUrl = $postId . "-" . $url;
        if ($post->getPostMedia() !==null) {
            $metadata["postMedia"] = $post->getPostMedia();
        }
        $metadata = ["postId" => $postId, "postTitle" => $post->getPostTitle(), "postTags" => $post->getPostTags(), "postUrl" => $fullUrl, "postAuthor" => $post->getPostAuthor()];
        $yaml = "---\n";
        foreach ($metadata as $key => $value) {
            if (is_array($value)) {
                $yaml .= $key . ":\n";
                foreach ($value as $k) {
                    $yaml .= "  - " - json_encode($k, JSON_UNESCAPED_UNICODE) . "\n";
                }
            } else {
                $yaml .= $key . ": " . json_encode((string)$value, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
        $yaml .= "---\n\n";
        file_put_contents($this->filePath ."/" . $postId . ".md", $yaml . $post->getPostText());
        return new PostData(postId: $postId, postTitle: $post->getPostTitle(), postTags: $post->getPostTags(), postMedia: $post->getPostMedia(), postText: $post->getPostText(), postUrl: $fullUrl, postAuthor: $post->getPostAuthor(), postDate: time());
    }

    public function deletePost($postId): bool {
        $path = $this->filePath ."/". $postId . ".md";
        if (!file_exists($path)) {
            return false;
        }
        return unlink($path);
    }

    public function loadPostFromFile(string $filePath): ?PostData {
        if (!file_exists($filePath)) {
            return null;
        }
        $data = file_get_contents($filePath);
        $metadata = $this->parser->parse($data);
        $text = $this->parser->removeMetadata($data);

        $postId = (int) ($metadata["postId"] ?? basename($filePath, ".md"));
        $url = $metadata["slug"] ?? $this->generateURLfriendly($metadata["title"] ?? basename($filePath, ".md"));
        $fullUrl = $postId ."-". $url;

        return new PostData(postId: $postId, postTitle: $metadata["postTitle"] ?? $fullUrl, postTags: $metadata["postTags"] ?? [], postMedia: $metadata["postMedia"] ?? null, postText: $text, postUrl: $fullUrl, postAuthor: $meta["postAuthor"] ?? null, postDate: filemtime($filePath) ?: null);
    }

    public function updatePost(PostData $post): bool {
        $postId = $post->getPostId();
        if ($postId === null) {
            return false;
        }
        $path = $this->filePath . "/" . $postId . ".md";
        if (!file_exists($path)) {
            return false;
        }
        $url = $this->generateURLfriendly($post->getPostTitle());
        if ($post->getPostMedia() !==null) {
            $metadata["postMedia"] = $post->getPostMedia();
        }
        $yaml = "---\n";
        $metadata = ["postId" => $postId, "postTitle" => $post->getPostTitle(), "postTags" => $post->getPostTags(), "postUrl" => $url, "postAuthor" => $post->getPostAuthor()];
        $yaml .= "---\n\n";
        file_put_contents($path, $yaml . $post->getPostText());
        return true;
    }
    
    public function findbyId(int $postId): ?PostData {
        return $this->loadPostFromFile($this->filePath . "/". $postId . ".md");
    }

    public function findByUrl(string $url): ?PostData {
        if(preg_match("/^(\d+)/", $url, $matches)) {
            return $this->findbyId((int) $matches[1]);
        }
        if (is_numeric($url)) {
            return $this->findbyId((int) $url);
        }
        return null;
    }
    public function findAll(): array {
        $posts = [];
        foreach (glob($this->filePath ."/*.md") as $file) {
            $post = $this->loadPostFromFile($file);
            if ($post === null) {
                continue;
            }
            $posts[] = $post;
        }
        usort($posts, fn(Postdata $a, Postdata $b) => ($b->getPostDate() ?? 0) - ($a->getPostDate() ?? 0));
        return $posts;
    }

    public function query(PostQuery $query): PostQueryResult {
        $posts = [];
        $wordsearch = $query->getWordSearch();
        $tagsearch = $query->getTagSearch();
        $authorsearch = $query->getAuthorSearch();
        foreach (glob($this->filePath ."/*.md") as $file) {
            $post = $this->loadPostFromFile($file);
            if ($post === null) {
                continue;
            }
            if ($authorsearch !== null && $post->getPostAuthor() !== $authorsearch) {
                continue;
            }
            if ($tagsearch !== null) {
                $tagsearch = trim($tagsearch);
                if ($tagsearch === "") {
                    continue;
                }

                $fileContent = file_get_contents($file);
                $metadata = $this->parser->parse($fileContent);
                $hit = false;
                foreach ($metadata["postTags"] ?? [] as $value) {
                    if (strtolower($value) === strtolower($tagsearch)) {
                        $hit = true;
                        break;
                    }
                }
            }

            if ($wordsearch !== null) {
                $wordsearch = trim($wordsearch);
                if ($wordsearch === "") {
                    continue;
                }
                $fileContent = file_get_contents($file);
                $metadata = $this->parser->parse($fileContent);
                $postBody = $this->parser->removeMetadata($fileContent);
                if (!$this->matchesWordsearch($wordsearch, $metadata, $postBody)) {
                    continue;
                }
            }
            $posts[] = $post;
        }
        usort($posts, fn(Postdata $a, Postdata $b) => ($b->getPostDate() ?? 0) - ($a->getPostDate() ?? 0));
        $resultNumber = count($posts) ?? 0;
        $postOnPage = array_slice($posts, ($query->getPage()-1)*$query->getLimit(), $query->getLimit());
        return new PostQueryResult($postOnPage, $resultNumber, $query);
    }

    public function getBestTags(int $limit = 3): array {
        $tags = [];
        foreach (glob($this->filePath ."/*.md") as $file) {
            $fileContent = file_get_contents($file);
            $metadata = $this->parser->parse($fileContent);
            $postTags = $metadata["postTags"] ?? [];
            foreach ($postTags as $tag) {
                $tags[strtolower($tag)] = ($tags[strtolower($tag)] ?? 0) +1;
            }
        }
        arsort($tags);
        return array_slice(array_keys($tags), 0, $limit);
    }

    public function generateURLfriendly(string $title): string {
        return preg_replace("/[^a-z0-9]+/","-", strtolower(trim($title)));
    }
    
    private function matchesWordSearch(string $word, array $metadata, string $postBody): bool {
        $word = strtolower($word);
        if (stripos($metadata["postTitle"] ?? "", $word) !== false) {
            return true;
        }
        if (stripos($postBody, $word) !== false) {
            return true;
        }
        foreach ($metadata["postTags"] ?? [] as $value) {
            if (stripos($value, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}