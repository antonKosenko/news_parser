<?php

namespace App\Models;

class ContentNewsUa extends ContentNews
{
    protected $table = 'content_news_ua';

    public function contentUa()
    {
        return $this->hasOne(News::class, 'news_id', 'news_id');
    }
}
