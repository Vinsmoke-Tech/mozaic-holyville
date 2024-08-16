<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctProfitDayReport extends Model
{
    // use HasFactory;
    protected $table        = 'acct_profit_day_report';
    protected $primaryKey   = 'balance_day_report_id';
    protected $guarded = [
        'last_update'
    ];
}
