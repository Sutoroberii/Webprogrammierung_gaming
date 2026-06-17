<?php

if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/parser/MarkdownParser.php";

require_once $abs_path . "/php/model/PostDAO.php";
require_once $abs_path . "/php/model/PostData.php";
require_once $abs_path . "/php/model/Post.php";

require_once $abs_path . "/php/model/MediaDAO.php";
require_once $abs_path . "/php/model/Media.php";

class PostController
{
    private MarkdownParser $markdownParser;
    private PostDAO $postDao;
    private MediaDAO $mediaDao;

    public function __construct()
    {
        $this->markdownParser = new MarkdownParser();
        $this->postDao = Post::getInstance();
        $this->mediaDao = Media::getInstance();
    }

    public function open(string $url): array
    {
        $post = $this->postDao->findByUrl($url);

        if ($post === null) {
            return [
                'found' => false,
                'post' => null,
                'html' => ''
            ];
        }

        return [
            'found' => true,
            'post' => $post,
            'html' => $this->markdownParser->parse($post->getPostText())
        ];
    }

    public function findById(int $id): ?PostData
    {
        return $this->postDao->findById($id);
    }

    public function isTitleAvailable(
        string $title,
        string $author,
        ?int $ignorePostId = null
    ): bool {
        foreach ($this->postDao->findAll() as $post) {
            if ($post->getPostAuthor() !== $author) {
                continue;
            }

            if (
                $ignorePostId !== null &&
                $post->getPostId() === $ignorePostId
            ) {
                continue;
            }

            if (
                strtolower($post->getPostTitle()) ===
                strtolower(trim($title))
            ) {
                return false;
            }
        }

        return true;
    }

    public function createNewPost(array $data): array
    {
        $errors = $this->validatePostData($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $title = trim($data['postTitle']);
        $text = $data['postText'];
        $author = $data['postAuthor'];

        $post = new PostData(
            postId: null,
            postTitle: $title,
            postTags: $this->parseTags($data['postTags'] ?? []),
            postMedia: '',
            postText: $text,
            postUrl: '',
            postAuthor: $author,
            postDate: null
        );

        $createdPost = $this->postDao->createPost($post);

        if (isset($data['postMedia'])) {
            $mediaPath = $this->uploadImage($createdPost->getPostId(), $data['postMediaFile']);
        } else {
            $mediaPath = '';
        }

        $this->postDao->updatePost(new PostData(
            postId: $createdPost->getPostId(),
            postTitle: $createdPost->getPostTitle(),
            postTags: $createdPost->getPostTags(),
            postMedia: $mediaPath,
            postText: $createdPost->getPostText(),
            postUrl: $createdPost->getPostUrl(),
            postDate: $createdPost->getPostDate(),
        ));

        $freshPost = $this->postDao->findById((int) $createdPost->getPostId());

        return [
            'success' => true,
            'post' => $freshPost ?? $createdPost
        ];
    }

    public function delete(int $id, string $author): array
    {
        $post = $this->postDao->findById($id);

        if ($post === null) {
            return [
                'success' => false,
                'errors' => ['Post existiert nicht']
            ];
        }

        if ($post->getPostAuthor() !== $author) {
            return [
                'success' => false,
                'errors' => ['Nur eigene Posts']
            ];
        }

        $this->postDao->deletePost($id);

        return [
            'success' => true
        ];
    }

    public function updatePost(array $data): array
    {
        $postId = (int) ($data['postId'] ?? 0);

        $oldPost = $this->postDao->findById($postId);

        if ($oldPost === null) {
            return [
                'success' => false,
                'errors' => ['Post existiert nicht']
            ];
        }

        $errors = $this->validatePostData($data, $postId);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }


        if (isset($data['postMedia'])) {
            $mediaPath = $this->uploadImage($postId, $data['postMediaFile']);
        } else {
            $mediaPath = '';
        }

        $post = new PostData(
            postId: $postId,
            postTitle: trim($data['postTitle']),
            postTags: $this->parseTags($data['postTags'] ?? []),
            postMedia: $mediaPath,
            postText: $data['postText'],
            postUrl: $oldPost->getPostUrl(),
            postAuthor: $data['postAuthor'],
            postDate: $oldPost->getPostDate()
        );

        $this->postDao->updatePost($post);

        return [
            'success' => true,
            'post' => $post
        ];
    }

    private function validatePostData(
        array $data,
        ?int $ignorePostId = null
    ): array {
        $errors = [];

        $title = trim($data['postTitle'] ?? '');
        $text = trim($data['postText'] ?? '');
        $author = $data['postAuthor'] ?? null;

        if ($title === '') {
            $errors[] = 'Titel ist nötig';
        }

        if ($text === '') {
            $errors[] = 'Text ist nötig';
        }

        if ($author === null || trim($author) === '') {
            $errors[] = 'Du musst eingeloggt sein';
        }

        if (
            $title !== '' &&
            $author !== null &&
            !$this->isTitleAvailable($title, $author, $ignorePostId)
        ) {
            $errors[] = 'Doppelte Titelbelegung';
        }

        if (isset($data['postMedia']) && $data['postMedia'] !== '' && !$this->validateImage($data['postMedia'])) {
            $errors[] = 'Das Bild ist nicht jpeg oder png oder kleiner als 1MB';
        }

        return $errors;
    }

    private function parseTags(string|array $tags): array
    {
        if (is_string($tags)) {
            $tags = array_map(
                'trim',
                explode(',', $tags)
            );
        }

        return array_values(
            array_filter(
                $tags,
                fn($tag) => trim((string) $tag) !== ''
            )
        );
    }

    private function uploadImage(int $postId, array $file): string
    {
        $uploadDir = __DIR__ . "/../../data/uploads/posts/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        $tmpName = $file['tmp_name'];
        $originalName = basename($file['name']);

        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = $postId . "." . $ext;

        $targetPath = $uploadDir . $fileName;

        move_uploaded_file($tmpName, $targetPath);

        return "/data/uploads/posts/$fileName";
    }

    private function generateURLfriendly(string $title): string
    {
        return trim(
            preg_replace(
                '/[^a-z0-9]+/',
                '-',
                strtolower(trim($title))
            ),
            '-'
        );
    }

    private function validateImage($file): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $maxSize = 1 * 1024 * 1024; // 1MB in Bytes
        if ($file['size'] > $maxSize) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);

        $allowedTypes = [
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        return true;
    }

}