<?php

namespace App\Models;

class ContentNewsRu extends ContentNews
{
    protected $table = 'content_news_ru';

    public function contentRu()
    {
        return $this->hasOne(News::class, 'news_id', 'news_id');
    }
}
