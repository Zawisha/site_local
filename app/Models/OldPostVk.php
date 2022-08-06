<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldPostVk extends Model
{
    use HasFactory;
    protected $table = 'old_posts_vk';
    protected $fillable = ['id','telegram_channel', 'post'];
}
