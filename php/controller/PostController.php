<?php
if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}
require_once $abs_path . "/php/model/Post.php";
require_once $abs_path . "/php/model/PostEntry.php";

class PostController {
    private function requireLogin(){
        if(!isset($_SESSION["loggedInUserId"])){
            $_SESSION["message"] = "login_required";
            header("Location: anmelden.php");
            exit;
        }
    }
    private function checkEntryParam() {
        if (!isset($_POST["author"]) || !isset($_POST["date"]) || !isset($_POST["game"]) || !isset($_FILES["media"])|| !isset($_POST["text"]) || !isset($_POST["submit"])) {
            $_SESSION["message"] = "missing_parameters";
            header("Location:index.php");
            exit;
        }
    }
    private function checkEntryRequiredParam() {
        if (empty($_POST["author"]) ||empty($_POST["date"]) ||empty($_POST["game"]) ||empty($_POST["text"]) ) {
            $_SESSION["message"] = "missing_required_parameters";
            foreach (["author", "date", "game", "text"] as $field) {
                $_SESSION[$field] = $_POST[$field];
            }
            return false;
        }
        return true;
    }
    private function checkPostId() {
        if (!isset($_REQUEST["postId"]) || !ctype_digit($_REQUEST["postId"])) {
            $this->handleMissingEntryException();
        }
    }

    private function handleInternalErrorException(){
        $_SESSION["message"] = "internal_error";
        header("Location: index.php");
        exit;
    }
    private function handleMissingEntryException() {
        $_SESSION["message"] = "invalid_entry_id";
        header("Location: index.php");
        exit;
    }
    

    public function createPost() {
        $this->requireLogin();
        $this->checkEntryParam();
        if ($this->checkEntryRequiredParam()) {
            header("Location: beitrag-neu.php");
            exit;
        }

        $mediaNew = null;

        if (isset($_FILES["media"]) && is_uploaded_file($_FILES["media"]["tmp_name"])) {
            $uploadFile = "images/posts/";

            $mediaNew = $uploadFile . basename($_FILES["media"]["name"]);

            if (!move_uploaded_file($_FILES["media"]["tmp_name"], $mediaNew)) {
                $_SESSION["message"] = "upload_error";  
            }
        } else {
            $mediaNew = $_SESSION["media"] ?? "";
        }

        try {
            $Post = Post::getInstance();
            $Post->createPost($_POST["author"], $_POST["date"], $_POST["game"], $mediaNew, $_POST["text"], $_SESSION["loggedInUserId"]);
            $_SESSION["message"] = "new_post";
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        } catch (Exception $exc) {
            $this->handleInternalErrorException();
        }
    }

    public function readPost() {
        $this->checkPostId();

        try {
            $postId = intval($_GET["postId"]);
            return Post::getInstance()->readPost($postId);

        } catch (Exception $exc) {
            $this->handleInternalErrorException();
        }
    }

    public function updatePost() {
        $this->requireLogin();
        $this->checkEntryParam();
        $this->checkPostId();

        if (!$this->checkEntryRequiredParam()) {
            $_SESSION["postId"] = $_POST["postId"];
            $encID = urlencode($_POST["postId"]);
            header("Location: beitrag-aendern-anzeige.php?postId=$encID");
            exit;
        }

        $mediaNew = null;
        if (isset($FILES["media"]) && is_uploaded_file($_FILES["media"]["tmp_name"])) {
            $uploadFile = "images/posts/";
            if(!is_dir($uploadFile)) {
                mkdir($uploadFile, 0777, true);
            }

            $mediaNew = $uploadFile . basename($_FILES["media"]["name"]);

            if (!move_uploaded_file($_FILES["media"]["tmp_name"], $mediaNew)) {
                $_SESSION["message"] = "upload_error";  
            }
        } else {
            $mediaNew = $_SESSION["media"] ?? "";
        }

        try {
            $post = Post::getInstance()->updatePost(
                $_POST["postId"],
                $_POST["author"],
                $_POST["date"],
                $_POST["game"],
                $mediaNew,
                $_POST["text"], 
                $_SESSION["loggedInUserId"]
            );
            $_SESSION["message"] = "update_post";
            return $post;
            
        } catch (MissingEntryException $exc) {
            $this->handleMissingEntryException();
        } catch (InternalErrorException $exc) {
            $this->handleInternalErrorException();
        }
    }

    public function deletePost() {
        $this->requireLogin();
        $this->checkPostId();

        try {
            Post::getInstance()->deletePost($_GET["postId"]);
        } catch (Exception $exc) {
            $this->handleInternalErrorException();
        } catch (MissingEntryException $exc) {
            $this->handleMissingEntryException();
        }
    }

    public function searchPosts($query) {
        $query = trim($query);
        if ($query === "") {
            return [];
        }
        try {
            return Post::getInstance()->searchPosts($query);
        } catch (Exception $exc) {
            $this->handleInternalErrorException();
        }    
    }



}



?>