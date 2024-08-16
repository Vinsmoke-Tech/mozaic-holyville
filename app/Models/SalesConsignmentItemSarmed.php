<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesConsignmentItemSarmed extends Model
{
    protected $connection   = 'mysql2';
    protected $table        = 'sales_consignment_item';
    protected $primaryKey   = 'sales_consignment_item_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
}
