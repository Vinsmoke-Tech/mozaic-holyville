<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalCategoriesFormula extends Model
{
    // use HasFactory;
    protected $table        = 'journal_categories_formula';
    protected $primaryKey   = 'categories_formula_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
