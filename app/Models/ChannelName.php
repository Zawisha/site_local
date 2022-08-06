<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelName extends Model
{
    use HasFactory;
    //многие к одному
    protected $table = 'chanell_name';
    protected $fillable = ['id', 'channel_name', 'technology_id'];
    public function get_tech()
    {
        return $this->hasOne('App\Models\TechList','id','technology_id');
    }
}
