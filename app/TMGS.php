<?php

namespace App;

class TMGS extends Reader
{
    protected $feedTitle = 'The Three-Minute Geek Show';
    protected $feedUrl = 'https://www.briefs.fm/the-three-minute-geek-show.xml';

    protected function parsePostId($post)
    {
        return (int) parent::parsePostId($post); 
    }
}
