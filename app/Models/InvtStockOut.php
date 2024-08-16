<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockOut extends Model
{
    protected $table        = 'invt_stock_out';
    protected $primaryKey   = 'stock_out_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
