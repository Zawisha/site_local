<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VKMessageText extends Model
{
    use HasFactory;
    protected $table = 'vk_message_text';
    protected $fillable = ['id','message_text', 'id_message','numb'];
}
