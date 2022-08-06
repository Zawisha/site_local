<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkWordsSearch extends Model
{
    use HasFactory;
    protected $table = 'vk_words_search';
    protected $fillable = ['id','search_word'];
}
