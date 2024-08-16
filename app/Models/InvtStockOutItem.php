<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockOutItem extends Model
{
    protected $table        = 'invt_stock_out_item';
    protected $primaryKey   = 'stock_out_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
