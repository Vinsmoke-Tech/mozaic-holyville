<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\CoreRecipe;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\InvtItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreRecipeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        Session::forget('items');
        $data = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id)
        ->get();

        return view('content.CoreRecipe.ListCoreRecipe', compact('data'));
    }

    public function getRecipe($item_id)
    {
        $data = CoreRecipe::select('item_id','quantity')
        ->where('core_recipe.data_state','=',0)
        ->where('item_menu_id', $item_id)
        ->where('core_recipe.company_id', Auth::user()->company_id)
        ->get();

        return $data;
    }

    public function addRecipe($item_id)
    {
        $categorys = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');

        $items2  = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name', 'item_id');

        $items  = InvtItem::where('item_id', $item_id)->first();

        $units     = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');

        $itemunits    = InvtItemUnit::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');

        $data = CoreRecipe::where('core_recipe.data_state','=',0)
        ->where('item_menu_id', $item_id)
        ->where('core_recipe.company_id', Auth::user()->company_id)
        ->get();

        return view('content.CoreRecipe.FormAddCoreRecipe', compact('categorys', 'items','items2', 'units','itemunits','data'));
    }
    
    public function getName($item_id)
    {
        $data = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function getUnit($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function processAddRecipe(Request $request)
    {
        $fields = $request->validate([
            'item_id'           => 'required',
            'item_unit_id'      => 'required',
            'quantity'          => 'required'

        ]);

        $data = CoreRecipe::create([
            'item_id'               => $fields['item_id'],
            'item_menu_id'          => $request['item_menu_id'],
            'item_unit_id'          => $fields['item_unit_id'],
            'quantity'              => $fields['quantity'],
            'company_id'            => Auth::user()->company_id,
            'updated_id'            => Auth::id(),
            'created_id'            => Auth::id(),
        ]);

        if($data->save()){
            $msg    = "Tambah Resep Berhasil";
            return redirect('/recipe/add-recipe/'.$request['item_menu_id'])->with('msg', $msg);
        } else {
            $msg    = "Tambah Resep Gagal";
            return redirect('/recipe/add-recipe/'.$request['item_menu_id'])->with('msg', $msg);
        }

    }

    public function processRecipe(Request $request)
    {

        $fields = $request->validate([
            'item_code'    => 'required',
            'quantity'     => 'required',
            'qty'          => 'required'

        ]);

        $receipe = CoreRecipe::where('company_id', Auth::user()->company_id)
        ->where('item_menu_id', $fields['item_code'])
        ->where('data_state', 0)
        ->get();

            $stock_item2 = InvtItemStock::where('item_id',$fields['item_code'])
            ->where('company_id', Auth::user()->company_id)
            ->first();
                $stock_item2->last_balance = $stock_item2['last_balance'] + $fields['qty'];
                $stock_item2->updated_id = Auth::id();
                $stock_item2->save();

            foreach($receipe as $val){
                $stock_item = InvtItemStock::where('item_id',$val->item_id)
                // ->where('item_id',$dataarray['item_id'])
                ->where('item_unit_id', $val['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                if(isset($stock_item)){
                    $stock_item->last_balance   = $stock_item['last_balance'] - $val['quantity'] * $fields['qty'];
                    $stock_item->updated_id     = Auth::id();
                        $stock_item->save();
                    }
        }
            $msg    = "Tambah Resep Berhasil";
            return redirect('/recipe')->with('msg', $msg);
            $msg    = "Tambah Resep Gagal";
            return redirect('/recipe')->with('msg', $msg);

    }
    
    public function deleteRecipe($recipe_id)
    {
        $table             = CoreRecipe::findOrFail($recipe_id);

        if($table->delete()){
            $msg    = "Hapus Resep Berhasil";
            return redirect('/recipe/add-recipe/'.$table['item_menu_id'])->with('msg', $msg);
        } else {
            $msg    = "Hapus Resep Gagal";
            return redirect('/recipe/add-recipe/'.$table['item_menu_id'])->with('msg', $msg);
        }

}

}