<?php

require_once __DIR__ . "/../model/UserDao.php";
require_once __DIR__ . "/../model/PostDao.php";
require_once __DIR__ . "/../model/PostQuery.php";

class UserController
{

    public function __construct(private UserDao $userDao, private PostDao $postDao)
    {
    }

    public function show(string $username): array
    {
        $user = $this->userDao->getByUsername($username);
        if ($user === null) {
            return ['found' => false, 'user' => null, 'posts' => []];
        }

        $result = $this->postDao->query(PostQuery::create()->usingAuthorsearch($username)->usingSort('newest')->usingLimit(100));
        return ['found' => true, 'user' => $user, 'posts' => $result->getResults()];
    }


}