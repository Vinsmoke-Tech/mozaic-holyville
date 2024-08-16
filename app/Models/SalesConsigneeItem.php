<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesConsigneeItem extends Model
{
    // use HasFactory;
    protected $table  = 'sales_consignee_item';
    protected $primaryKey = 'sales_consignee_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
