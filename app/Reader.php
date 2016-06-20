<?php

namespace App;

abstract class Reader
{
    protected $feedTitle;
    protected $feedUrl;
    private $rss;
    private $feed;

    public function __construct(Rss $rss)
    {
        $this->rss = $rss;
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
        return $this->getPosts()->filter(function ($post) use ($first, $last) {
            return $this->parsePostid($post) >= $first && $this->parsePostId($post) <= $last;
        });
    }

    public function postsBetween($first, $last)
    {
        return array_merge(
            ['<h3>' . strtoupper($this->feedTitle) . '</h3>'],
            $this->getPostsBetween($first, $last)->map(function ($post) {
                return $this->postToHtml($post);
            })->all()
        );
    }

    private function postToHtml($post)
    {
        return sprintf("<h3><a href=\"%s\">%s</a></h3>", $post['permalink'], $post['title']);
    }

    abstract protected function parsePostId($post);
}
