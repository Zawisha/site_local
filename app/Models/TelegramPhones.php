<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramPhones extends Model
{
    protected $table = 'phone_telegram';
    protected $fillable = ['id','phone', 'api_id', 'api_hash','proxy_adres','proxy_port','proxy_username','proxy_password'];
}
