<?php

namespace App;

class FMGS
{
    private $feedTitle = 'The Five-Minute Geek Show';
    private $feedUrl = 'https://simplecast.com/podcasts/335/rss';
    private $rss;
    private $feed;

    public function __construct(Rss $rss)
    {
        $this->rss = $rss;
    }

    private function getPosts()
    {
        return $this->getFeed()->posts;
    }

    private function getFeed()
    {
        if (! $this->feed) {
            $this->feed = $this->rss->parse($this->feedUrl);
        }

        return $this->feed;
    }

    public function postsBetween($first, $last)
    {
        $posts = $this->getPosts()->filter(function ($post) use ($first, $last) {
            return $this->parsePostid($post) >= $first && $this->parsePostId($post) <= $last;
        })->map(function ($post) {
            return $this->postToHtml($post);
        })->all();

        return array_merge(['<h3>' . strtoupper($this->feedTitle) . '</h3>'], $posts);
   }

    private function parsePostId($post)
    {
        return (int) collect(explode('/', $post['permalink']))->last();
    }

    private function postToHtml($post)
    {
        return sprintf("<h3><a href=\"%s\">%s</a></h3>", $post['permalink'], $post['title']);
    }
}
