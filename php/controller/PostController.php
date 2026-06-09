<?php

require_once __DIR__ . "/../parser/MarkdownParser.php";
require_once __DIR__ . "/../model/Media.php";
require_once __DIR__ . "/../model/PostData.php";
require_once __DIR__ . "/../model/Post.php";

class PostController
{
    private MarkdownParser $markdownParser;
    private PostDao $postDao;
    private MediaDao $mediaDao;

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
            'html' => $this->markdownParser->parse(
                $post->getPostText()
            )
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
            postTags: $this->parseTags(
                $data['postTags'] ?? []
            ),
            postMedia: null,
            postText: $text,
            postUrl: '',
            postAuthor: $author,
            postDate: null
        );

        $createdPost = $this->postDao->createPost($post);

        $this->updatePostMedia(
            $createdPost,
            $data,
            $author
        );

        return [
            'success' => true,
            'post' => $createdPost
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

        return ['success' => true];
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

        $errors = $this->validatePostData(
            $data,
            $postId
        );

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $mediaPath = $this->determineMediaPath(
            $oldPost,
            $data
        );

        $post = new PostData(
            postId: $postId,
            postTitle: trim($data['postTitle']),
            postTags: $this->parseTags(
                $data['postTags'] ?? []
            ),
            postMedia: $mediaPath,
            postText: $data['postText'],
            postUrl: $postId . '-' .
            $this->generateURLfriendly(
                $data['postTitle']
            ),
            postAuthor: $data['postAuthor'],
            postDate: null
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

        if (
            $title !== '' &&
            $author !== null &&
            !$this->isTitleAvailable(
                $title,
                $author,
                $ignorePostId
            )
        ) {
            $errors[] = 'Doppelte Titelbelegung';
        }

        return $errors;
    }

    private function parseTags(
        string|array $tags
    ): array {

        if (is_string($tags)) {
            $tags = array_map(
                'trim',
                explode(',', $tags)
            );
        }

        return array_values(
            array_filter(
                $tags,
                fn($tag) => $tag !== ''
            )
        );
    }

    private function determineMediaPath(
        PostData $oldPost,
        array $data
    ): ?string {

        $mediaPath = $oldPost->getPostMedia();

        if (isset($data['postMediaFile'])) {

            $uploaded = $this->handleMediaUpload(
                $data['postMediaFile'],
                (string) $oldPost->getPostId(),
                $oldPost->getPostAuthor()
            );

            if ($uploaded !== null) {
                $mediaPath = $uploaded;
            }

        } elseif (!empty($data['postMedia'])) {

            $mediaPath = $data['postMedia'];
        }

        return $mediaPath;
    }

    private function updatePostMedia(
        PostData $post,
        array $data,
        string $author
    ): void {

        $mediaPath = null;

        if (isset($data['postMediaFile'])) {

            $mediaPath = $this->handleMediaUpload(
                $data['postMediaFile'],
                (string) $post->getPostId(),
                $author
            );

        } elseif (!empty($data['postMedia'])) {

            $mediaPath = $data['postMedia'];
        }

        if ($mediaPath === null) {
            return;
        }

        $updatedPost = new PostData(
            postId: $post->getPostId(),
            postTitle: $post->getPostTitle(),
            postTags: $post->getPostTags(),
            postMedia: $mediaPath,
            postText: $post->getPostText(),
            postUrl: $post->getPostUrl(),
            postAuthor: $post->getPostAuthor(),
            postDate: $post->getPostDate()
        );

        $this->postDao->updatePost($updatedPost);
    }

    private function handleMediaUpload(
        array $fileData,
        string $postId,
        string $username
    ): ?string {

        if (
            !isset($fileData['error']) ||
            $fileData['error'] !== UPLOAD_ERR_OK
        ) {
            return null;
        }

        return $this->mediaDao->saveMedia(
            $username,
            $postId,
            [
                'type' => $fileData['type'],
                'data' => base64_encode(
                    file_get_contents(
                        $fileData['tmp_name']
                    )
                )
            ]
        );
    }

    private function generateURLfriendly(
        string $title
    ): string {

        return trim(
            preg_replace(
                '/[^a-z0-9]+/',
                '-',
                strtolower(trim($title))
            ),
            '-'
        );
    }
}