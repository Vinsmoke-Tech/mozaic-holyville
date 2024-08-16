<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtStockOut;
use App\Models\InvtStockOutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtStockOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('datases');
        Session::forget('arraydatases');
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
        
        $data = InvtStockOut::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('stock_out_date','>=',$start_date)
        ->where('stock_out_date','<=',$end_date)
        ->get();
        return view('content.InvtStockOut.ListInvtStockOut', compact('start_date','end_date','data'));
    }

    public function filterStockOut(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/stock-out');
    }

    public function resetFilterStockOut()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/stock-out');
    }

    public function addStockOut()
    {
        $category_list = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        $datases = Session::get('datases');
        $data_array = Session::get('arraydatases');
        return view('content.InvtStockOut.FormAddInvtStockOut', compact('category_list','datases','data_array'));
    }

    public function addElementsStockOut(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['stock_out_date']      = '';
            $datases['stock_out_remark']    = '';
        }
        $datases[$request->name] = $request->value;
        $datases = Session::put('datases', $datases);
    }

    public function addArrayStockOut(Request $request)
    {
        $request->validate([
            'item_category_id'  => 'required',
            'item_id'           => 'required',
            'item_unit_id'      => 'required',
            'quantity'          => 'required|numeric',
            'default_quantity'  => 'required'
        ]);

        $arraydatases = array(
            'item_category_id'  => $request->item_category_id,
            'item_id'           => $request->item_id,
            'item_unit_id'      => $request->item_unit_id,
            'quantity'          => $request->quantity,
            'default_quantity'  => $request->default_quantity,
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

        return redirect('/stock-out/add');
    }

    public function addDeleteArrayStockOut($record_id)
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

        return redirect('/stock-out/add');
    }

    public function addDeleteelEmentsStockOut()
    {
        Session::forget('datases');
        Session::forget('arraydatases');

        return redirect('/stock-out/add');
    }

    public function getItemName($item_id)
    {
        $data   = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id', $item_category_id)->first();
        
        return $data['item_category_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function processAddStockOut(Request $request)
    {
        $request->validate([
            'stock_out_date'    => 'required',
            'stock_out_remark'  => '',
        ]);

        $stock_out = InvtStockOut::create([
            'company_id' => Auth::user()->company_id,
            'stock_out_date' => $request->stock_out_date,
            'stock_out_remark' => $request->stock_out_remark,
            'created_id' => Auth::id(),
            'updated_id' => Auth::id(),
        ]);

        if ($stock_out->save()) {
            $stock_out_id = InvtStockOut::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at','DESC')
            ->first();
            $data_array = Session::get('arraydatases');
            foreach ($data_array as $key => $val) {
                $data_stock = InvtItemStock::where('item_id',$val['item_id'])
                ->where('item_category_id',$val['item_category_id'])
                ->where('item_unit_id', $val['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->where('data_state', 0)
                ->first();
                InvtStockOutItem::create([
                    'stock_out_id'      => $stock_out_id['stock_out_id'],
                    'item_stock_id'     => $data_stock['item_stock_id'],
                    'item_category_id'  => $val['item_category_id'],
                    'item_id'           => $val['item_id'],
                    'item_unit_id'      => $val['item_unit_id'],
                    'quantity'          => $val['quantity'],
                    'company_id'        => Auth::user()->company_id,
                    'created_id'        => Auth::id(),
                    'updated_id'        => Auth::id(),
                ]);

                if(isset($data_stock)){
                    $table = InvtItemStock::findOrFail($data_stock['item_stock_id']);
                    $table->last_balance = $data_stock['last_balance'] - $val['quantity'];
                    $table->updated_id = Auth::id();
                    $table->save();
                }
            }
            $msg = 'Tambah Pengurangan Stok Berhasil';
            return redirect('/stock-out/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Pengurangan Stok Gagal';
            return redirect('/stock-out/add')->with('msg',$msg);
        }
    }

    public function detailStockOut($stock_out_id)
    {
        $data = InvtStockOut::where('stock_out_id',$stock_out_id)
        ->first();
        $data_item = InvtStockOutItem::where('stock_out_id', $stock_out_id)
        ->get();
        return view('content.InvtStockOut.DetailInvtStockOut', compact('data','data_item'));
    }
}
