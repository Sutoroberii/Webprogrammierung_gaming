<?php
require_once __DIR__ . "/../parser/MarkdownParser.php";
require_once __DIR__ . "/../model/Media.php";
require_once __DIR__ . "/../model/PostData.php";
require_once __DIR__ . "/../model/Post.php";


class PostController {
    private MarkdownParser $markdownParser;
    private PostDao $postDao;
    private MediaDao $mediaDao;

    public function __construct() {
        $this->postDao = Post::getInstance();
        $this->markdownParser = new MarkdownParser();
        $this->mediaDao = Media::getInstance();
    }

    public function open(string $url): array {
        $post = $this->postDao->findByUrl($url);

        if ($post === null) {
            return [
                'found' => false,
                'post'  => null,
                'html'  => '',
            ];
        }

        return [
            'found' => true,
            'post'  => $post,
            'html'  => $this->markdownParser->parse($post->getPostText()),
        ];
    }

    public function findById(int $id): ?PostData {
        return $this->postDao->findbyId($id);
    }

    
    public function isTitleAvailable(string $title, string $author): bool {
        foreach ($this->postDao->findAll() as $post) {
            if ($post->getPostAuthor() !== $author) {
                continue;
            }
            if (strtolower($post->getPostTitle()) === strtolower(trim($title))) {
                return false;
            }
        }
        return true;
    }
    public function createNewPost(array $data): array
    {
        $errors = [];

        $title = trim($data['postTitle'] ?? '');
        $tags = $data['postTags'] ?? [];
        $media = $data['postMedia'] ?? null;
        $text = $data['postText'] ?? '';
        $author = $data['postAuthor'] ?? null;

        if ($title === '') {
            $errors[] = 'Titel ist nötig';
        }

        if ($text === '') {
            $errors[] = 'Text ist nötig';
        }

        if ($author === null) {
            $errors[] = 'Du musst eingeloggt sein';
        }

        if ($title !== '' && $author !== null && !$this->isTitleAvailable($title, $author)) {
            $errors[] = 'Doppelte Titelbelegung';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $tagList = [];
        if (is_string($tags)) {
            $tagList = array_map('trim', explode(',', $tags));
        } elseif (is_array($tags)) {
            $tagList = $tags;
        }
        $tagList = array_values(array_filter($tagList, fn ($tag) => $tag !== ''));

        $post = new PostData(postId: null, postTitle: $title, postTags: $tagList, postMedia: null, postText: $text, postUrl: '', postAuthor: $author, postDate: null);

        $current = $this->postDao->createPost($post);

        $mediaFilePath = null;
        if (isset($data['postMediaFile'])) {
            $uploaded = $this->handleMediaUpload($data['postMediaFile'], $current->getPostId(), $author);
            if ($uploaded !== null) {
                $mediaFilePath = $uploaded;
            }
        } elseif ($media !== '') {
            $mediaFilePath = $media;
        }

        $this->postDao->updatePost(new PostData(postId: $current->getPostId(), postTitle: $current->getPostTitle(), postTags: $current->getPostTags(), postMedia: $mediaFilePath, postText: $current->getPostText(), postUrl: $current->getPostUrl(), postDate: $current->getPostDate()));

        return ["success" => true, "post" => $current];
    }

    public function delete(int $id, string $author): array {
        $oldPost = $this->postDao->findById($id);

        if ($oldPost === null) {
            return ['success' => false, 'errors' => ["Post existiert nicht"]];
        }

        if ($oldPost->getPostAuthor() !== $author) {
            return ['success' => false, 'errors' => ["Nur eigene Posts"]];
        }
        $this->postDao->deletePost($id);
        return ['success' => true];
    }

    public function updatePost(array $data): array {
        $errors = [];

        $id = (int) ($data['postId'] ?? 0);
        $title = trim($data['postTitle'] ?? '');
        $tags = $data['postTags'] ?? [];
        $media = $data['postMedia'] ?? null;
        $text = $data['postText'] ?? '';
        $author = $data['postAuthor'] ?? null;

        if ($id <= 0) {
            $errors[] = "Post ID ungültig";
        }

        if ($title === '') {
            $errors[] = "Titel ist nötig";
        }

        if ($text === '') {
            $errors[] = "Text ist nötig";
        }

        if ($author === null) {
            $errors[] = "Du musst eingeloggt sein";
        }

        $oldPost = $id > 0 ? $this->postDao->findById($id) : null;
        if ($oldPost === null) {
            $errors[] = "Post exisitert nicht";
        }

        if ($oldPost !== null && $oldPost->getPostAuthor() !== $author) {
            $errors[] = "nur eigene Posts";
        }

        if ($title !== '' && $author !== null && !$this->isTitleAvailable($title, $author)) {
            $errors[] = "Doppelte Titelbelegung";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $tagList = [];
        if (is_string($tags)) {
            $tagList = array_map('trim', explode(',', $tags));
        } elseif (is_array($tags)) {
            $tagList = $tags;
        }
        $tagList = array_values(array_filter($tagList, fn ($tag) => $tag !== ''));

        $mediaFilePath = $oldPost->getPostMedia();
        if (isset($data["postMedia"])) {
            $uploaded = $this->handleMediaUpload($data['postMedia'], $id, $author);
            if ($uploaded !== null) {
                $mediaFilePath = $uploaded;
            }
        } elseif ($media !== '') {
            $mediaFilePath = $media;
        }

        $url = $id . '-' . $this->generateURLfriendly($title);

        $post = new PostData(postId: $id, postTitle: $title, postTags: $tagList, postMedia: $mediaFilePath, postText: $text, postUrl: $url, postAuthor: $author, postDate: null);

        $this->postDao->updatePost($post);

        return ['success' => true, 'post' => $post];
    }

    private function handleMediaUpload(array $fileData, string $postId, string $username): ?string {
        if (!isset($fileData['error']) || $fileData['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $filename = $this->mediaDao->saveMedia($username, $postId, [ 'type' => $fileData['type'], 'data' => base64_encode(file_get_contents($fileData['tmp_name']))]);
        return $filename;
    }

    private function generateURLfriendly(string $title): string {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title))), '-');
    }
}
