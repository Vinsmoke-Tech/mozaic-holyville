<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacheCup extends Model
{
    // use HasFactory;
    protected $table        = 'cache_cup';
    protected $primaryKey   = 'id_cup';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
