<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $table = 'ranking'; // Nombre real de tu tabla
    protected $fillable = ['nombre', 'email', 'racha_maxima'];
}
