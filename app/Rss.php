<?php

namespace App;

use SimplePie;

class Rss
{
    public function parse($url)
    {
        $feed = new SimplePie;
        $feed->set_feed_url($url);
        $feed->enable_cache(false);
        $feed->init();
        $feed->handle_content_type();

        $info = [];

        return (object) [
            'info' => $info,
            'posts' => $this->getPosts($feed)
        ];
    }

    private function getPosts($feed)
    {
        return collect($feed->get_items())->map(function ($item) {
            return [
                'title' => $item->get_title(),
                'content' => $item->get_content(),
                'permalink' => $item->get_permalink(),
                'date' => $item->get_date('r'),
            ];
        });
    }
}
