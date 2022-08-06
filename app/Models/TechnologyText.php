<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnologyText extends Model
{
    use HasFactory;
    protected $table = 'text_of_technology';
    protected $fillable = ['id','technology_id', 'text_technology'];
}
