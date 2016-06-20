<?php

namespace App;

abstract class Reader
{
    protected $feedTitle;
    protected $feedUrl;
    protected $select = false;
    private $rss;
    private $feed;

    public function __construct(Rss $rss)
    {
        $this->rss = $rss;
    }

    public function postsBetween($first, $last)
    {
        $posts = $this->getPostsBetween($first, $last)->map(function ($post) {
            return $this->postToHtml($post);
        });

        if ($posts->count() === 0) {
            return [];
        }

        return array_merge(
            ['<h3 style="margin-top: 1em;">' . strtoupper($this->feedTitle) . '</h3>'],
            $posts->all()
        );
    }

    protected function getPosts()
    {
        return $this->getFeed()->posts->sortByDesc('date_unix');
    }

    private function getFeed()
    {
        if (! $this->feed) {
            $this->feed = $this->rss->parse($this->feedUrl);
        }

        return $this->feed;
    }

    protected function getPostsBetween($first, $last)
    {
        return $this->getPosts()->sortBy('date_unix')->filter(function ($post) use ($first, $last) {
            return $this->shouldFilter($post, $first, $last);
        })->reverse();
    }

    protected function shouldFilter($post, $first, $last)
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

    /* Only works for integers
    protected function getPostsBetween($first, $last)
    {
        return $this->getPosts()->filter(function ($post) use ($first, $last) {
            return $this->parsePostid($post) >= $first && $this->parsePostId($post) <= $last;
        });
    }
    */

    private function postToHtml($post)
    {
        return sprintf("<h3><a href=\"%s\">%s</a></h3>", $post['permalink'], $post['title']);
    }

    protected function parsePostId($post)
    {
        return collect(explode('/', $post['permalink']))->last();
    }
}
