<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralDev extends Model
{
    protected $table = 'list_of_telegram_coders';
    protected $fillable = ['id','user_id', 'username', 'write','technology_id'];
}
