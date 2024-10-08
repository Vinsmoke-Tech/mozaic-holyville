<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceSarmed extends Model
{
    protected $connection   = 'mysql2';
    protected $table        = 'sales_invoice';
    protected $primaryKey   = 'sales_invoice_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
}
