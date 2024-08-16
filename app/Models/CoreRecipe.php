<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreRecipe extends Model
{

    protected $table        = 'core_recipe'; 
    protected $primaryKey   = 'recipe_id';
    
    protected $guarded = [
        'created_at',
        'updated_at',
    ];
}