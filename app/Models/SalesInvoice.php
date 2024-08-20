<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $table        = 'sales_invoice';
    protected $primaryKey   = 'sales_invoice_id';
    protected $fillable = [
        'sales_invoice_id',
        'company_id',
        'customer_name',
        'customer_id',
        'sales_invoice_no',
        'sales_invoice_date',
        'subtotal_item',
        'subtotal_amount',
        'discount_percentage_total',
        'discount_amount_total',
        'ppn_percentage_total',
        'ppn_amount_total',
        'total_amount',
        'shortover_amount',
        'owing_amount',
        'paid_amount',
        'change_amount',
        'last_balance',
        'table_no',
        'payment_method',
        'data_state',
        'created_at',
        'updated_at',
        'created_id',
        'updated_id',
        'sales_status',
    ];
    protected $guarded = [];
    public function member()
    {
        return $this->hasOne(CoreMember::class, 'member_id', 'customer_id');
    }
    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id', 'sales_invoice_id');
    }
    public function journal() {
        return $this->hasOne(JournalVoucher::class,'transaction_journal_no','sales_invoice_no');
    }
     protected static function booted()
     {
         $userid=Auth::id();
         static::updated(function (SalesInvoice $model) use($userid) {
             $model->updated_id = $userid;
         });
     }
}
