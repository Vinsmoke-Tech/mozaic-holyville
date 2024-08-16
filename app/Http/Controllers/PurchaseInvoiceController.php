<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SalesConsigneeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function PHPUnit\Framework\isEmpty;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        if(!$purchase_method = Session::get('purchase_method')){
            $purchase_method = null;
        } else {
            $purchase_method = Session::get('purchase_method');
        }
        Session::forget('datases');
        Session::forget('arraydatases');
        
        if($purchase_method == ''){
            $data = PurchaseInvoice::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->where('purchase_invoice_date', '>=', $start_date)
            ->where('purchase_invoice_date', '<=', $end_date)
            ->get();
        }else{
            $data = PurchaseInvoice::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->where('purchase_invoice_date', '>=', $start_date)
            ->where('purchase_invoice_date', '<=', $end_date)
            ->where('purchase_method', $purchase_method)
            ->get();
        }

        $purchase_method_list = array(
            1 => 'Pembelian Biasa',
            2 => 'Pembelian Konsinyasi'
        );

        return view('content.PurchaseInvoice.ListPurchaseInvoice', compact('data','start_date','end_date','purchase_method_list','purchase_method'));
    }

    public function addPurchaseInvoice()
    {
        $datases = Session::get('datases');
        $arraydatases = Session::get('arraydatases');

        $categorys = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        $items     = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name', 'item_id');
        $units     = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $warehouses = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');

        $purchase_method_list = [
            1 => 'Pembelian Biasa',
            2 => 'Pembelian Konsinyasi'
        ];
        // echo json_encode($datases);exit; 
        return view('content.PurchaseInvoice.FormAddPurchaseInvoice', compact('categorys', 'items', 'units','warehouses','datases','arraydatases','purchase_method_list'));
    }

    
    public function detailPurchaseInvoice($purchase_invoice_id)
    {
        $warehouses = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $purchase_invoice_id)->first();
        $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchase_invoice_id)->get();
        return view('content.PurchaseInvoice.DetailPurchaseInvoice', compact('purchaseinvoice','warehouses','purchaseinvoiceitem'));
    }

    public function addElementsPurchaseInvoice(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['purchase_invoice_supplier']   = '';
            $datases['warehouse_id']                = '';
            $datases['purchase_invoice_date']       = '';
            $datases['purchase_method']             = '';
            $datases['purchase_invoice_remark']     = '';
        }
        $datases[$request->name] = $request->value;
        Session::put('datases', $datases);
    }

    public function addArrayPurchaseInvoice(Request $request)
    {
        $request->validate([
            'item_category_id'  => 'required',
            'item_id'           => 'required',
            'item_unit_id'      => 'required',
            'item_unit_cost'    => 'required',
            'quantity'          => 'required',
            'subtotal_amount'   => 'required',
            'item_expired_date' => 'required'
        ]);

        $arraydatases = array(
            'item_category_id'  => $request->item_category_id,
            'item_id'           => $request->item_id,
            'item_unit_id'      => $request->item_unit_id,
            'item_unit_cost'    => $request->item_unit_cost,
            'quantity'          => $request->quantity,
            'subtotal_amount'   => $request->subtotal_amount,
            'item_expired_date' => $request->item_expired_date
        );

        $lastdatases = Session::get('arraydatases');
        if($lastdatases !== null){
            array_push($lastdatases, $arraydatases);
            Session::put('arraydatases', $lastdatases);
        } else {
            $lastdatases = [];
            array_push($lastdatases, $arraydatases);
            Session::push('arraydatases', $arraydatases);
        }

        return redirect('/purchase-invoice/add');
    }

    public function deleteArrayPurchaseInvoice($record_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('arraydatases');
        
        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('arraydatases');
        Session::put('arraydatases', $arrayBaru);

        return redirect('/purchase-invoice/add');
    }
    
    public function processAddPurchaseInvoice(Request $request)
    {
        $transaction_module_code = 'PBL';
        $transaction_module_id = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'purchase_invoice_supplier' => 'required',
            'warehouse_id'              => 'required',
            'purchase_invoice_date'     => 'required',
            'purchase_invoice_remark'   => 'required',
            'subtotal_item'             => 'required',
            'subtotal_amount_total'     => 'required',
            'total_amount'              => 'required',
            'paid_amount'               => 'required',
            'purchase_method'           => 'required',
            'owing_amount'              => 'required'
        ]);
        if (empty($request->discount_percentage_total)){
            $discount_percentage_total = 0;
            $discount_amount_total = 0;
        }else{
            $discount_percentage_total = $request->discount_percentage_total;
            $discount_amount_total = $request->discount_amount_total;
        }
        $datases = array(
            'purchase_invoice_supplier' => $fields['purchase_invoice_supplier'],
            'warehouse_id'              => $fields['warehouse_id'],
            'purchase_invoice_date'     => $fields['purchase_invoice_date'],
            'purchase_invoice_remark'   => $fields['purchase_invoice_remark'],
            'subtotal_item'             => $fields['subtotal_item'],
            'discount_percentage_total' => $discount_percentage_total,
            'discount_amount_total'     => $discount_amount_total,
            'subtotal_amount_total'     => $fields['subtotal_amount_total'],
            'total_amount'              => $fields['total_amount'],
            'paid_amount'               => $fields['paid_amount'],
            'owing_amount'              => $fields['owing_amount'],
            'purchase_method'           => $fields['purchase_method'],
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        );

        if(PurchaseInvoice::create($datases)){

        $purchase_invoice_id = PurchaseInvoice::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
    
        if ($fields['purchase_method'] == 1) {
            $journal = array(
                'company_id'                    => Auth::user()->company_id,
                'transaction_module_id'         => $transaction_module_id,
                'transaction_module_code'       => $transaction_module_code,
                'journal_voucher_status'        => 1,
                'journal_voucher_date'          => $fields['purchase_invoice_date'],
                'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                'journal_voucher_period'        => date('Ym'),
                'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                'created_id'                    => Auth::id(),
                'updated_id'                    => Auth::id()
            );
            JournalVoucher::create($journal);
        }
    
            $arraydatases = Session::get('arraydatases');
            foreach ($arraydatases as $key => $val) {
                $dataarray = array(
                    'purchase_invoice_id'   => $purchase_invoice_id['purchase_invoice_id'],
                    'item_category_id'      => $val['item_category_id'],
                    'item_unit_id'          => $val['item_unit_id'],
                    'item_id'               => $val['item_id'],
                    'quantity'              => $val['quantity'],
                    'item_unit_cost'        => $val['item_unit_cost'],
                    'subtotal_amount'       => $val['subtotal_amount'],
                    'item_expired_date'     => $val['item_expired_date'],
                    'company_id'            => Auth::user()->company_id,
                    'created_id'            => Auth::id(),
                    'updated_id'            => Auth::id()
                );
                $dataStock = array(
                    'warehouse_id'      => $fields['warehouse_id'],
                    'item_id'           => $val['item_id'],
                    'item_unit_id'      => $val['item_unit_id'],
                    'item_category_id'  => $val['item_category_id'],
                    'item_unit_id'      => $val['item_unit_id'],
                    'last_balance'      => $val['quantity'],
                    'last_update'       => date('Y-m-d H:i:s'),
                    'company_id'        => Auth::user()->company_id,
                    'created_id'        => Auth::id(),
                    'updated_id'        => Auth::id()
                );
            
                PurchaseInvoiceItem::create($dataarray);
                $stock_item = InvtItemStock::where('item_id',$dataarray['item_id'])
                ->where('warehouse_id', $dataStock['warehouse_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('item_unit_id', $dataarray['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                if(isset($stock_item)){
                    $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                    $table->last_balance = $dataStock['last_balance'] + $stock_item['last_balance'];
                    $table->updated_id = Auth::id();
                    $table->save();
                } else {
                    InvtItemStock::create($dataStock);
                }
            }

            if ($fields['purchase_method'] == 1) {
                $account_setting_name = 'purchase_cash_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
                
                $account_setting_name = 'purchase_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);
        } 
            $msg = 'Tambah Pembelian Berhasil';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Pembelian Gagal';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        }
    }
    
    public function processDeletePurchaseInvoice($purchase_invoice_id)
    {
        $purchaseinvoice = PurchaseInvoice::findOrFail($purchase_invoice_id);
        $purchaseinvoice->data_state = 1;
        $purchaseinvoice->deleted_at = date('Y-m-d');
        $purchaseinvoice->deleted_id = Auth::id();

        if($purchaseinvoice->save()){
            $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchaseinvoice['purchase_invoice_id'])
            ->get();

            foreach($purchaseinvoiceitem as $key => $val){
                $itemstock = InvtItemStock::where('item_id', $val['item_id'])
                ->where('warehouse_id', $purchaseinvoice['warehouse_id'])
                ->first();

                $itemstock->last_balance = $itemstock['last_balance']-$val['quantity'];
                $itemstock->save();
            }

            $transaction_module_code = 'HPBL';
            $transaction_module_id   = $this->getTransactionModuleID($transaction_module_code);

            if (empty($request->discount_percentage_total)){
                $discount_percentage_total  = 0;
                $discount_amount_total      = 0;
            }else{
                $discount_percentage_total  = $request->discount_percentage_total;
                $discount_amount_total      = $request->discount_amount_total;
            }

            $journal = array(
                'company_id'                    => Auth::user()->company_id,
                'transaction_module_id'         => $transaction_module_id,
                'transaction_module_code'       => $transaction_module_code,
                'journal_voucher_status'        => 1,
                'journal_voucher_date'          => date('Y-m-d'),
                'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                'journal_voucher_period'        => date('Ym'),
                'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                'created_id'                    => Auth::id(),
                'updated_id'                    => Auth::id()
            );

            if(JournalVoucher::create($journal)){
                $account_setting_name   = 'purchase_cash_account';
                $account_id             = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id     = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                
                if($account_setting_status == 1){
                    $account_setting_status = 0;
                } else {
                    $account_setting_status = 1;
                }
                if ($account_setting_status == 0){
                    $debit_amount = $purchaseinvoice['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $purchaseinvoice['total_amount'];
                }

                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $purchaseinvoice['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
                
                $account_setting_name   = 'purchase_account';
                $account_id             = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id     = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                
                if($account_setting_status == 1){
                    $account_setting_status = 0;
                } else {
                    $account_setting_status = 1;
                }
                if ($account_setting_status == 0){
                    $debit_amount = $purchaseinvoice['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $purchaseinvoice['total_amount'];
                }

                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $purchaseinvoice['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);
    
                $msg = 'Hapus Pembelian Berhasil';
                return redirect('/purchase-invoice')->with('msg',$msg);
            } else {
                $msg = 'Hapus Pembelian Gagal';
                return redirect('/purchase-invoice')->with('msg',$msg);
            }
        }
    }

    public function processDeleteConsignee($purchase_invoice_id)
    {
        $purchaseinvoice = PurchaseInvoice::findOrFail($purchase_invoice_id);
        $purchaseinvoice->data_state = 1;
        $purchaseinvoice->deleted_at = date('Y-m-d');
        $purchaseinvoice->deleted_id = Auth::id();

        if($purchaseinvoice->save()){
                $msg = 'Hapus Pembelian Berhasil';
                return redirect('/purchase-invoice')->with('msg',$msg);
            } else {
                $msg = 'Hapus Pembelian Gagal';
                return redirect('/purchase-invoice')->with('msg',$msg);
            }
    }

    public function processResetConsignee($purchase_invoice_id)
    {
        $purchaseinvoice = PurchaseInvoice::findOrFail($purchase_invoice_id);
        $purchaseinvoice->data_state = 1;
        $purchaseinvoice->deleted_at = date('Y-m-d');
        $purchaseinvoice->deleted_id = Auth::id();

        if($purchaseinvoice->save()){

            $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchaseinvoice['purchase_invoice_id'])
            ->get();

            foreach($purchaseinvoiceitem as $key => $val){
                $itemstock = InvtItemStock::where('item_id', $val['item_id'])
                ->where('warehouse_id', $purchaseinvoice['warehouse_id'])
                ->first();

                $itemstock->last_balance = 0;
                $itemstock->save();
            }

                $msg = 'Hapus Pembelian Berhasil';
                return redirect('/purchase-invoice')->with('msg',$msg);
            } else {
                $msg = 'Hapus Pembelian Gagal';
                return redirect('/purchase-invoice')->with('msg',$msg);
            }
    }
    

    public function getWarehouseName($warehouse_id)
    {
        $data = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();

        return $data['warehouse_name'];
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function filterPurchaseInvoice(Request $request)
    {
        $start_date         = $request->start_date;
        $end_date           = $request->end_date;
        $purchase_method    = $request->purchase_method;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('purchase_method', $purchase_method);

        return redirect('/purchase-invoice');
    }

    public function addResetPurchaseInvoice()
    {
        Session::forget('datases');
        Session::forget('arraydatases');
        return redirect('/purchase-invoice/add');
    }

    public function filterResetPurchaseInvoice()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('purchase_method');

        return redirect('/purchase-invoice');
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'];
    }

    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_name'];
    }

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'];
    }

    public function getStock($item_id)
    {
        $data = InvtItemStock::where('data_state',0)
        ->where('item_id',$item_id)
        ->first();

        return $data['last_balance'];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();

        return $data['account_default_status'];
    }

    public function getLastBalance($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_unit_price'];
    }

    public function detailPaidPurchaseInvoice($purchase_invoice_id)
    {
        $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $purchase_invoice_id)
        ->where('purchase_method', 2)
        ->first();

        $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchase_invoice_id)
        ->where('data_state',0)
        ->get();



        $purchaseinvoicestock = InvtItem::select('item_unit_price','item_id')
        ->where('data_state',0)
        ->first();

        // dd($purchaseinvoicestock);
        $purchase_method_list = array(
            1 => 'Pembelian Biasa',
            2 => 'Pembelian Konsinyasi'
        );  
        // dd($purchaseinvoiceitem);
        return view('content.PurchaseInvoice.FormAddPaidPurchaseInvoice', compact('purchaseinvoice','purchaseinvoiceitem', 'purchase_method_list','purchaseinvoicestock'));
    }

    public function addPaidPurchaseInvoice(Request $request)
    {
        // dd($request->all());
        $transaction_module_code = 'PJK';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);

        $fields = $request->validate([
            'journal_voucher_date'        => 'required',

        ]);
        
        // PurchaseInvoice::where('purchase_invoice_id', $request->purchase_invoice_id)
        // ->update(['payment_status' => '1']);
        
        foreach($request->item as $val) {
            SalesConsigneeItem::create([
            'company_id'            => Auth::user()->company_id,
            'item_id'               => $val['item_id'],
            'purchase_invoice_id'   => $request->purchase_invoice_id,
            'item_category_id'      => $val['item_category_id'],
            'item_unit_id'          => $val['item_unit_id'],
            'consignee_date'        => $fields['journal_voucher_date'],
            'quantity'              => $val['quantity'],
            'item_unit_cost'        => $val['item_unit_cost'],
            'subtotal_amount'       => $val['subtotal_amount'],
            'item_sold'             => $val['item_sold'], 
            'item_not_sold'         => ($val['quantity'] - $val['item_sold']), 
            'consignment_profits'   => (($val['item_unit_price'] - $val['item_unit_cost']) * $val['item_sold']),
            'depositor_profit'      => ($val['item_sold'] * $val['item_unit_cost']),
            'gross_profit'          => ($val['item_sold'] * $val['item_unit_price'])]);
            
            
        $stock_item2 = InvtItemStock::where('item_id',$val['item_id'])
        ->where('item_unit_id', $val['item_unit_id'])
        ->where('company_id', Auth::user()->company_id)
        ->first();

            $stock_item2->last_balance = $stock_item2['last_balance'] - $val['item_sold'];
            $stock_item2->updated_id = Auth::id();
            $stock_item2->save();
        }


        $sales_invoice_id   = PurchaseInvoice::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'transaction_journal_no'        => $sales_invoice_id['purchase_invoice_no'],
            'journal_voucher_date'          => $fields['journal_voucher_date'],
            'journal_voucher_segment'       => 1,
            'journal_voucher_period'        => date('Ym'),
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucher::create($journal);

        if($journal){
            $arraydatases       = Session::get('data_itemses');
                $account_setting_name   = 'sales_menu_cash';
                $account_id             = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                
                $journal_voucher_id     = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                $salesinvoiceitem       =  PurchaseInvoice::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();
                // echo json_encode($salesinvoiceitem); exit;

                    if ($account_setting_status == 0){
                        $debit_amount   =$request->total;
                        $credit_amount  = 0;
                    } else {
                        $debit_amount   = 0;
                        $credit_amount  =$request->total;
                    }

                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request->total,
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => Auth::id(),
                    'created_id'                    => Auth::id()
                );
        // echo json_encode($journal_debit); exit;

                JournalVoucherItem::create($journal_debit);
    
                $account_setting_name   = 'sales_menu_consignee';
                $account_id             = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id     = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                $salesinvoice           = PurchaseInvoice::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();

                if ($account_setting_status == 0){
                    $debit_amount = $request->total;
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request->total;
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request->total,
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => Auth::id(),
                    'created_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);


            $msg = 'Tambah Invoice Penjualan Berhasil';
            return redirect('/purchase-invoice/')->with('msg',$msg);
        } else {
            $msg = 'Tambah Invoice Penjualan Gagal';
            return redirect('/purchase-invoice/')->with('msg',$msg);
        }
    }

}