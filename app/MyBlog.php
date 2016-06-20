<?php

namespace App;

class MyBlog extends Reader 
{
    protected $feedTitle = 'The blog';
    protected $feedUrl = 'https://mattstauffer.co/blog/feed.atom';
    protected $select = false;

    protected function getPostsBetween($first, $last)
    {
        return $this->getPosts()->sortBy('date_unix')->filter(function ($post) use ($first, $last) {
            return $this->shouldFilter($post, $first, $last);
        })->reverse();
    }

    private function shouldFilter($post, $first, $last)
    {
        // Handle edge case when there's only one post, so $first and $last are equal
        if ($first == $last && $this->select) {
            $this->select = false;
            return false;
        }

        if ($this->parsePostId($post) == $first) {
            $this->select = true;
            return true;
        }

        if ($this->parsePostId($post) == $last) {
            $this->select = false;
            return true;
        }

        return $this->select;
    }

    protected function parsePostId($post)
    {
        return collect(explode('/', $post['permalink']))->last();
    }
}
