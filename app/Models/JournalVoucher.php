<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    // use HasFactory;
    protected $table        = 'acct_journal_voucher';
    protected $primaryKey   = 'journal_voucher_id';
    public function items() {
        return $this->hasMany(JournalVoucherItem::class, "journal_voucher_id", "journal_voucher_id");
    }
    public function sales() {
        return $this->belongsTo(SalesInvoice::class, "invoice_id","sales_invoice_id");
    }
    public function salesNo() {
        return $this->belongsTo(SalesInvoice::class, "transaction_journal_no","sales_invoice_no");
    }
    public function purchase() {
        return $this->belongsTo(JournalVoucherItem::class, "journal_voucher_id", "invoice_id");
    }
    public function balanceDetail() {
        return $this->hasMany(AcctAccountBalanceDetail::class,'transaction_id','journal_voucher_id');
     }
    public function user() {
       return $this->belongsTo(User::class,'created_id','user_id');
    }
    protected $guarded = [
        'journal_voucher_id'
    ];
}
