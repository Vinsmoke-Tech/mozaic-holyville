<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockTransfer extends Model
{
    protected $table        = 'invt_stock_transfer';
    protected $primaryKey   = 'stock_transfer_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
