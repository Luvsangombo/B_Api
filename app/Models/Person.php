<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable=['e-mail', 'password'];
    use HasFactory;
    protected $table = 'people';
}
