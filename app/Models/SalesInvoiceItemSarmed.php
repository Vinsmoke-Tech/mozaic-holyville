<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItemSarmed extends Model
{
    protected $connection   = 'mysql2';
    protected $table        = 'sales_invoice_item';
    protected $primaryKey   = 'sales_invoice_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
