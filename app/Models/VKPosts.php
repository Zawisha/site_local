<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VKPosts extends Model
{
    use HasFactory;
    protected $table = 'vk_posts';
    protected $fillable = ['id','group_id', 'last_post'];
}
