<?php

namespace App\Http\Controllers;

use App\Models\SystemLogUser;
use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemStock;
use Illuminate\Support\Facades\Auth;


class PublicController extends Controller
{

    public function testing()
    {
        print_r('TES');
        exit;
    }
    
	public function set_log($user_id, $username, $id, $class, $pk, $remark){

		date_default_timezone_set("Asia/Jakarta");

		$log = array(
			'user_id'		=>	$user_id,
			'username'		=>	$username,
			'id_previllage'	=> 	$id,
			'class_name'	=>	$class,
			'pk'			=>	$pk,
			'remark'		=> 	$remark,
			'log_stat'		=>	'1',
			'log_time'		=>	date("Y-m-d G:i:s")
		);
		return SystemLogUser::create($log);
	}

	public function optionItem($item_category_id)
	{
		$item = InvtItem::where('item_category_id', $item_category_id)
		->where('data_state', 0)
		->where('company_id', Auth::user()->company_id)
		->get();

		$data = '';
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($item as $mp){
            $data .= "<option value='$mp[item_id]'>$mp[item_name]</option>\n";	
        }
        return $data;
	}

	public function optionItemUnit($item_id)
	{
		$unit = InvtItemStock::join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'invt_item_stock.item_unit_id')
		->join('invt_item', 'invt_item.item_id', '=', 'invt_item_stock.item_id')
        ->where('invt_item_stock.data_state', 0)
        ->where('invt_item.item_id', $item_id)
        ->where('invt_item_stock.company_id', Auth::user()->company_id)
        ->get();
        
        $data = '';
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($unit as $mp){
            $data .= "<option value='$mp[item_unit_id]'>$mp[item_unit_name]</option>\n";	
        }
        return $data;
	}

	public function getItemPrice($item_id)
	{
		$data = InvtItem::where('item_id', $item_id)
		->where('data_state', 0)
		->where('company_id', Auth::user()->company_id)
		->first();


			return $data['item_unit_price'];

	}

	public function getItemSalesPrice($item_id)
	{
		$data = InvtItem::where('item_id', $item_id)
		->where('data_state', 0)
		->where('company_id', Auth::user()->company_id)
		->first();

		if ($data->item_status == 0) {

			return $data['item_unit_price'];

		} else {

			$item_price = 0;
			foreach (json_decode($data['item_data'], true) as $key => $val) {
				$data = InvtItem::where('item_id', $val['item_id'])
				->where('data_state', 0)
				->where('company_id', Auth::user()->company_id)
				->first();

				$item_price += $data['item_unit_price'] * $val['quantity'];
			}


			return $item_price;
		}
	}
}
