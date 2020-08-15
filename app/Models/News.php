<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    public $timestamps = false;

    public static $urlMask = "http://k.img.com.ua/rss/{%lang%}/ukraine.xml";
    public static $languages = ['ru', 'ua'];
    public static $image_path = 'storage/app/public/news_image';

    public function contentRu()
    {
        return $this->belongsTo(ContentNewsRu::class,  'news_id', 'news_id');
    }

    public function contentUa()
    {
        return $this->belongsTo(ContentNewsUa::class, 'news_id', 'news_id');
    }
}
