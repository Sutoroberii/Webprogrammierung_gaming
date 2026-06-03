<?php
if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

require_once $abs_path . "/php/model/PostDAO.php";
require_once $abs_path . "/php/model/PostEntry.php";

class PostSession implements PostDAO {
    private static $instance = null;

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new PostSession();
        }
        return self::$instance;
    }

    private $post = array();

    private function __construct()
    {
        if(isset($_SESSION["post"])) {
            $this->post = unserialize($_SESSION["post"]);
        } else {
            $this->post[0] = new PostEntry("max", "20.05.2025", "Call of Duty", "image.jpg", "This is a test post.", 0, 0);
            $_SESSION["post"] = serialize($this->post);
            $_SESSION["nextPostId"] = 1;
        }
    }

    #[Override]
    public function 
    createPost($author, $date, $game, $media, $text, $userId) {
        $this->post[$_SESSION["nextPostId"]] = new PostEntry($author, $date, $game, $media, $text, $userId, $_SESSION["nextPostId"]);
        $_SESSION["nextPostId"] = $_SESSION["nextPostId"] + 1;
        $_SESSION["post"] = serialize($this->post);
    }

    public function readPost($postId) {
        foreach($this->post as $post) {
            if($post->getPostId() == $postId) {
                return $post;
            }
        }
        throw new MissingEntryException();
    }

    public function updatePost($postId, $author, $date, $game, $media, $text, $userId) {
        foreach($this->post as $post) {
            if($post->getPostId() == $postId) {
                if($post->getUserId() != $userId) {
                    throw new InternalErrorException();
                }
                $post->update($postId, $author, $date, $game, $media, $text, $userId);
                $_SESSION["post"] = serialize($this->post);
                return $post;
            }
        }
        throw new MissingEntryException();
    }

    public function deletePost($postId) {
        foreach($this->post as $index => $post) {
            if($post->getPostId() == $postId) {
                unset($this->post[$index]);
                $_SESSION["post"] = serialize($this->post);
                return;
            }
        }
        throw new MissingEntryException();
    }

    public function getPostsFromUser($userId) {
        $userPosts = array();
        foreach($this->post as $post) {
            if($post->getUserId() == $userId) {
                $userPosts[] = $post;
            }
        }
        return $userPosts;
    }

    public function searchPosts($query) {
        $matchingPosts = array();
        foreach($this->post as $post) {
            if(strpos($post->getText(), $query) !== false || strpos($post->getGame(), $query) !== false) {
                $matchingPosts[] = $post;
            }
        }
        return $matchingPosts;
    }
}
?>
