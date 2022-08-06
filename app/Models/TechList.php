<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechList extends Model
{
    protected $table = 'list_of_technology';
    protected $fillable = ['id','technology'];
}
