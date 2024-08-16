<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalCategories extends Model
{
    // use HasFactory;
    protected $table        = 'journal_categories';
    protected $primaryKey   = 'journal_categories_id';
    protected $guarded = [
        'last_update',
        'updated_at',
        'created_at'
    ];
}
