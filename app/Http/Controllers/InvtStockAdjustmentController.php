<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtStockAdjustment;
use App\Models\InvtStockAdjustmentItem;
use App\Models\InvtWarehouse;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtStockAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('category_id');
        Session::forget('item_id');
        Session::forget('unit_id');
        Session::forget('warehouse_id');
        Session::forget('date');
        Session::forget('datases');
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data  = InvtStockAdjustment::join('invt_stock_adjustment_item','invt_stock_adjustment.stock_adjustment_id','=','invt_stock_adjustment_item.stock_adjustment_id')
        ->where('invt_stock_adjustment.stock_adjustment_date', '>=', $start_date)
        ->where('invt_stock_adjustment.stock_adjustment_date', '<=', $end_date)
        ->where('invt_stock_adjustment.company_id', Auth::user()->company_id)
        ->where('invt_stock_adjustment.data_state',0)
        ->get(); 
        return view('content.InvtStockAdjustment.ListInvtStockAdjustment',compact('data','start_date','end_date'));
    }

    public function addStockAdjustment()
    {
        if(!$category_id = Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }


        $categorys  = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouse  = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units      = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items      = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');
        $datasess   = Session::get('datases');

        $data = InvtItemStock::join('invt_item', 'invt_item.item_id', '=', 'invt_item_stock.item_id')
        ->join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item_stock.item_category_id')
        ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'invt_item_stock.item_unit_id')
        ->join('invt_warehouse', 'invt_warehouse.warehouse_id', '=', 'invt_item_stock.warehouse_id')
        ->where('invt_item.data_state',0)
        ->where('invt_item_stock.item_category_id',$category_id)
        ->where('invt_item_stock.company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtStockAdjustment.FormAddInvtStockAdjustment', compact('categorys', 'units', 'items', 'datasess', 'data','warehouse','category_id'));
    }

    public function addElementsStockAdjustment(Request $request)
    {
        $datasess = Session::get('datases');
        if(!$datasess || $datasess == ''){
            $datasess['item_category_id']        = '';
            $datasess['item_id']                 = '';
            $datasess['item_unit_id']            = '';
            $datasess['warehouse_id']            = '';
            $datasess['stock_adjustment_date']   = '';
        }

        $datasess[$request->name] = $request->value;
        $datasess = Session::put('datases',$datasess);
    }

    public function filterAddStockAdjustment(Request $request)
    {
        $request->validate([
            'item_category_id'      => 'required',
        ]);
        $category_id = $request->item_category_id;

        Session::put('category_id', $category_id);

        return redirect('/stock-adjustment/add');
    }

    public function filterListStockAdjustment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/stock-adjustment');
    }

    public function getItemName($item_id)
    {
        $data   = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getWarehouseName($warehouse_id)
    {
        $data   = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();

        return $data['warehouse_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data   = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function getItemStock($item_id,$item_category_id)
    {
        $data = InvtItemStock::where('item_id',$item_id)
        ->where('item_category_id',$item_category_id)
        ->first();
        // dd($data);exit;
        return $data['last_balance'];
    }

    public function processAddStockAdjustment(Request $request)
    {
        $total_no = $request->total_no;
        $successFlag = false; // Flag to track if any checkbox is selected

        for ($i = 1; $i <= $total_no; $i++) {
        
        if( $request['checkbox_' . $i] == 1){

        $data_header = InvtStockAdjustment::create([

            'stock_adjustment_date' => date('Y-m-d'),
            'warehouse_id'          => 5,
            'company_id'            => Auth::user()->company_id,
            'created_id'            => Auth::id(),
            'updated_id'            => Auth::id()
            ]);
            }
        }

        $stock_adjustment_id   = InvtStockAdjustment::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();

        for ($i = 1; $i <= $total_no; $i++) {
            if( $request['checkbox_' . $i] == 1){
                    $dataArray = array(
                        'stock_adjustment_id'           => $stock_adjustment_id['stock_adjustment_id'],
                        'item_id'                       => $request['item_id_' . $i],
                        'item_category_id'              => $request['item_category_id_' . $i],
                        'item_unit_id'                  => $request['item_unit_id'],
                        'last_balance_data'             => $request['last_balance_data_' . $i],
                        'last_balance_physical'         => $request['last_balance_physical_' . $i],
                        'last_balance_adjustment'       => $request['last_balance_adjustment_' . $i],
                        'stock_adjustment_item_remark'  => $request['stock_adjustment_item_remark'],
                        'company_id'                    => Auth::user()->company_id,
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id(),
                    );
                // echo json_encode($dataArray);exit;
                InvtStockAdjustmentItem::create($dataArray); 

                $stock_item = InvtItemStock::where('item_id',$dataArray['item_id'])
                ->where('item_category_id',$dataArray['item_category_id'])
                ->where('warehouse_id', $data_header['warehouse_id'])
                ->first();
                // echo json_encode($stock_item);exit;
                
                if(isset($stock_item)){
                    $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);      
                    $table->last_balance = $dataArray['last_balance_adjustment'];
                    $table->updated_id = Auth::id();
                    $table->save();
                }
                $successFlag = true; // Set flag to true if at least one checkbox is selected
            }
        }  

        if ($successFlag) {
            $msg = 'Tambah Stock Berhasil..';
        } else {
            $msg = 'Tambah Stock Gagal. Harap pilih setidaknya satu item!';
        }

        return redirect('/stock-adjustment/add')->with('msg',$msg);
    }

    public function addReset(){
        Session::forget('category_id');
        Session::forget('item_id');
        Session::forget('unit_id');
        Session::forget('warehouse_id');
        Session::forget('date');
        Session::forget('datases');

        return redirect('/stock-adjustment/add');
    }

    public function listReset()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/stock-adjustment');
    }

    public function detailStockAdjustment($stock_adjustment_id)
    {
        $categorys  = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouse  = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units      = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items      = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');

        $data = InvtStockAdjustment::select('invt_stock_adjustment_item.item_id', 'invt_stock_adjustment_item.item_category_id', 'invt_stock_adjustment_item.item_unit_id', 'invt_stock_adjustment_item.last_balance_adjustment', 'invt_stock_adjustment_item.last_balance_physical', 'invt_stock_adjustment_item.stock_adjustment_item_remark', 'invt_stock_adjustment.stock_adjustment_date', 'invt_stock_adjustment.stock_adjustment_date', 'invt_stock_adjustment_item.last_balance_data')
        ->join('invt_stock_adjustment_item', 'invt_stock_adjustment_item.stock_adjustment_id', '=', 'invt_stock_adjustment.stock_adjustment_id')
        ->where('invt_stock_adjustment.stock_adjustment_id', $stock_adjustment_id)
        ->where('invt_stock_adjustment.data_state', 0)
        ->first();
        return view('content.InvtStockAdjustment.DetailInvtStockAdjustment',compact('categorys','warehouse','units','items','data'));
    }
}

