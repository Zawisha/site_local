<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramPhones extends Model
{
    protected $table = 'phone_telegram';
    protected $fillable = ['id','phone', 'api_id', 'api_hash'];
}
