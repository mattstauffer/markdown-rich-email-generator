<?php

namespace App;

class TightenBlog extends Reader 
{
    protected $feedTitle = 'Tighten blog';
    protected $feedUrl = 'https://blog.tighten.co/feed';

    protected function shouldFilter($post, $first, $last)
    {
        if (parent::shouldFilter($post, $first, $last)) {
            if ($post['author']->name != 'Matt Stauffer') {
                return false;
            }

            return true;
        }
    } 
}
