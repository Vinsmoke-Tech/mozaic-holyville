<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalCategoriesFormulaAccount extends Model
{
    // use HasFactory;
    protected $table        = 'journal_categories_formula_account';
    protected $primaryKey   = 'categories_formula_account_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
