<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentNews extends Model
{
    protected $fillable = ['news_id', 'title', 'url', 'description', 'date_public'];

    public $timestamps = false;
    public static $modelsByLanguage = ['ua' => 'ContentNewsUa', 'ru' => 'ContentNewsRu'];
}
