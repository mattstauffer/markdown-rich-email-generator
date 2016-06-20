<?php

namespace App;

class FMGS extends Reader
{
    protected $feedTitle = 'The Five-Minute Geek Show';
    protected $feedUrl = 'https://simplecast.com/podcasts/335/rss';

    protected function parsePostId($post)
    {
        return (int) parent::parsePostId($post); 
    }
}
