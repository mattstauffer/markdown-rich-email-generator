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

        if ($feed->error()) {
            throw new \Exception($feed->error());
        }

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
                'author' => $item->get_author(),
                'content' => $item->get_content(),
                'permalink' => $item->get_permalink(),
                'date' => $item->get_date('r'),
                'date_unix' => $item->get_date('U'),
            ];
        });
    }
}
