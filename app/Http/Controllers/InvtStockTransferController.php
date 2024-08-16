<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtStockTransfer;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Session;

class InvtStockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        Session::forget('arraydatases');
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

        $data = InvtStockTransfer::select('stock_transfer_id', 'transfer_date', 'transfer_remark', 'transfer_no')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('transfer_date', '>=', $start_date)
        ->where('transfer_date', '<=', $end_date)
        ->get();

        return view('content.InvtStockTransfer.ListInvtStockTransfer', compact('start_date', 'end_date', 'data'));
    }

    public function filterStockTransfer(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/stock-transfer');
    }

    public function resetFilterStockTransfer()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/stock-transfer');
    }

    public function addStockTransfer()
    {
        $category_list = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        $datases = Session::get('datases');
        $dataArray = Session::get('arraydatases');

        return view('content.InvtStockTransfer.FormAddInvtStockTransfer', compact('category_list', 'datases', 'dataArray'));
    }

    public function addArrayStockTransfer(Request $request)
    {
        $request->validate([
            'item_category_id'  => 'required',
            'item_unit_id'      => 'required',
            'item_id'           => 'required',
            'quantity'          => 'required',
            'type'              => 'required',
        ]);

        $data = array(
            'item_category_id'  => $request->item_category_id,
            'item_unit_id'      => $request->item_unit_id,
            'item_id'           => $request->item_id,
            'quantity'          => $request->quantity,
            'type'              => $request->type,
        );

        Session::push('arraydatases', $data);

    }

    public function addDeleteArrayStockTransfer($record_id)
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

        return redirect('/stock-transfer/add');
    }

    public function addElementsStockTransfer(Request $request)
    {
        $datasess = Session::get('datases');
        if(!$datasess || $datasess == ''){
            $datasess['transfer_date']        = '';
            $datasess['transfer_remark']      = '';
        }

        $datasess[$request->name] = $request->value;
        $datasess = Session::put('datases',$datasess);
    }

    public function addDeleteelEmentsStockTransfer()
    {
        Session::forget('datases');
        Session::forget('arraydatases');

        return redirect('stock-transfer/add');
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

    public function processAddStockTransfer(Request $request)
    {
        $request->validate([
            'transfer_date' => 'required',
        ]);

        $dataArray = Session::get('arraydatases');

        $transfer = InvtStockTransfer::create([
            'company_id' => Auth::user()->company_id,
            'transfer_date' => $request->transfer_date,
            'transfer_remark' => $request->transfer_remark,
            'transfer_data' => json_encode($dataArray),
            'created_id' => Auth::id(),
            'updated_id' => Auth::id(),
        ]);

        if ($transfer == true) {
            foreach ($dataArray as $key => $val) {
                if ($val['type'] == 'ingredient') {

                    $data_stock = InvtItemStock::where('item_id', $val['item_id'])
                    ->where('item_category_id', $val['item_category_id'])
                    ->where('item_unit_id', $val['item_unit_id'])
                    ->where('company_id', Auth::user()->company_id)
                    ->where('data_state', 0)
                    ->first();

                    $table = InvtItemStock::findOrFail($data_stock['item_stock_id']);
                    $table->last_balance = $data_stock['last_balance'] - $val['quantity'];
                    $table->updated_id = Auth::id();
                    $table->save();

                } else if ($val['type'] == 'menu') {

                    $data_stock = InvtItemStock::where('item_id', $val['item_id'])
                    ->where('item_category_id', $val['item_category_id'])
                    ->where('item_unit_id', $val['item_unit_id'])
                    ->where('company_id', Auth::user()->company_id)
                    ->where('data_state', 0)
                    ->first();

                    $table = InvtItemStock::findOrFail($data_stock['item_stock_id']);
                    $table->last_balance = $data_stock['last_balance'] + $val['quantity'];
                    $table->updated_id = Auth::id();
                    $table->save();

                }
            }

            $msg = 'Tambah Penggunaan Bahan Baku Berhasil';
            return redirect('/stock-transfer/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Penggunaan Bahan Baku Gagal';
            return redirect('/stock-transfer/add')->with('msg',$msg);
        }
    }

    public function detailStockTransfer($stock_transfer_id)
    {
        $data = InvtStockTransfer::where('stock_transfer_id', $stock_transfer_id)
        ->first();
        $dataItem = json_decode($data->transfer_data, true);
        // echo json_encode($data);exit;

        return view('content.InvtStockTransfer.FormDetailInvtStockTransfer', compact('data', 'dataItem'));
    }

    public function printStockTransfer()
    {
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

        // $data = InvtStockTransfer::select('stock_transfer_id')
        // ->first();
        $data2 = InvtStockTransfer::select('transfer_no','stock_transfer_id','transfer_date','transfer_remark')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('transfer_date', '>=', $start_date)
        ->where('transfer_date', '<=', $end_date)
        ->get();

        // * return raw (eloquent data)
        $data = InvtStockTransfer::select('transfer_data','stock_transfer_id')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('transfer_date', '>=', $start_date)
        ->where('transfer_date', '<=', $end_date)
        ->get();
        //* code below not used
        // $dataItem = json_decode($data, true);
        // echo json_encode($dataItem);exit;
        // return $data2;
        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::setHeaderCallback(function($pdf){
            $pdf->SetFont('helvetica', '', 8);
            $header = "
            <div></div>
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr>
                        <td rowspan=\"3\" width=\"76%\"><img src=\"\" width=\"120\"></td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Halaman</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".$pdf->getAliasNumPage()." / ".$pdf->getAliasNbPages()."</div></td>
                    </tr>
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Dicetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".ucfirst(Auth::user()->name)."</div></td>
                    </tr>
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Tgl. Cetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".date('d-m-Y H:i')."</div></td>
                    </tr>
                </table>
                <div style=\"margin-bottom: 150px;\">
                    <hr>
                </div>
            ";

            $pdf->writeHTML($header, true, false, false, false, '');
        });

        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(15, 25, 10, 10); // put space of 10 on top


        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENGGUNAAN BAHAN BAKU</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        // $pdf::SetMargins(30, 10, 10, 10);
        $pdf::writeHTML($tbl, true, false, false, false, '');

        $no = 1;
        $tblStock7 =" ";
        foreach ($data2 as $rows) {
        $tblStock7 = "
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
            <tr>
                <td width=\"13%\" style=\"text-align:left\">No Penggunaan</td>
                <td width=\"2%\" style=\"text-align:left\">:</td>
                <td width=\"15%\" style=\"text-align:left\">".$rows->transfer_no."</td>
            </tr>
            <tr>
                <td width=\"13%\" style=\"text-align:left\">Tanggal</td>
                <td width=\"2%\" style=\"text-align:left\">:</td>
                <td style=\"text-align:left\">".$rows->transfer_date."</td>
            </tr>
            <tr>
                <td width=\"13%\" style=\"text-align:left\">Keterangan</td>
                <td width=\"2%\" style=\"text-align:left\">:</td>
                <td style=\"text-align:left\">".$rows->transfer_remark."</td>
            </tr>
        </table>
        ";
        $no++;
        $pdf::writeHTML($tblStock7, true, false, false, false, '');
            
        $no = 1;
        $tblStock1 =" ";
        
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" width=\"100%\">
        <tr border=\"0\" >
            <td width=\"85%\" style=\"text-align:center; font-size:11px; font-weight: bold\">Bahan Baku</td>
        </tr>
        </table>

        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Kategori</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Barang</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Satuan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Jumlah</div></td>

            </tr>
             ";
             $no++;

        $no = 1;
        $tblStock2 =" ";
        // * get the data where 'stock_transfer_id' equal to currently iterated loop (loop ln326) get only 'transfer_data' and return the data at index 0 in order to convert to collection
        $dataItem = collect(json_decode($data->where('stock_transfer_id',$rows->stock_transfer_id)->pluck('transfer_data')[0]));
        // $dataItem = $data->where('stock_transfer_id',$rows->stock_transfer_id)->pluck('transfer_data')[0];
        // $dataItem = json_decode($dataItem);
        // * try dump data below and comment data above for testing
        // $dataItem = collect(json_decode($data->where('stock_transfer_id',$rows->stock_transfer_id)->pluck('transfer_data')));
        // $dataItem = $data->where('stock_transfer_id',$rows->stock_transfer_id)->pluck('transfer_data');
        // return $dataItem;
        // dump($dataItem);exit;
        foreach ($dataItem as $val) {
                    if ($val->type == 'ingredient'){
                        $tblStock2 .="
                        <tr>
                        
                        <td style=\"text-align:center\">$no.</td>
                        <td style=\"text-align:center\">".$this->getCategoryName($val->item_category_id)."</td>
                        <td style=\"text-align:center\">".$this->getItemName($val->item_id)."</td>
                        <td style=\"text-align:center\">".$this->getItemUnitName($val->item_unit_id)."</td>
                        <td style=\"text-align:center\">".$val->quantity."</td>
                        </tr>

                        ";
                        $no++;
            }
        }

        $tblStock3 = "

        </table>";
        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');
        $no = 1;
        $tblStock4 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
        <tr border=\"0\" >
            <td width=\"85%\" style=\"text-align:center; font-size:11px; font-weight: bold\">Menu Jadi</td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Kategori</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Barang</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Satuan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Jumlah</div></td>

            </tr>

             ";

        $no = 1;
        $tblStock5 =" ";
        foreach ($dataItem as  $val) {
                if ($val->type == 'menu'){
                    $tblStock5 .="
                    <tr>
                        <td style=\"text-align:center\">$no.</td>
                        <td style=\"text-align:center\">".$this->getCategoryName($val->item_category_id)."</td>
                        <td style=\"text-align:center\">".$this->getItemName($val->item_id)."</td>
                        <td style=\"text-align:center\">".$this->getItemUnitName($val->item_unit_id)."</td>
                        <td style=\"text-align:center\">".$val->quantity."</td>
                    </tr>

                    ";
                    $no++;
                }
        }

        $tblStock6 = "
        </table> ";


        $pdf::writeHTML($tblStock4.$tblStock5.$tblStock6, true, false, false, false, '');
    }

        $filename = 'Laporan_Pembelian_Anggota_pdf';
        $pdf::Output($filename, 'I');
    }

     //excel
     public function exportStockTransfer()
     {
        //  ini_set('max_execution_time', 13000);
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

        $data2 = InvtStockTransfer::select('transfer_no','stock_transfer_id','transfer_date','transfer_remark')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('transfer_date', '>=', $start_date)
        ->where('transfer_date', '<=', $end_date)
        ->get();

        // * return raw (eloquent data)
        $data = InvtStockTransfer::select('transfer_data','stock_transfer_id')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('transfer_date', '>=', $start_date)
        ->where('transfer_date', '<=', $end_date)
        ->get();
        //  $dataItem = json_decode($data, true);
        //  echo json_encode($dataItem);exit;
        
            $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator("IBS CJDW")
        ->setLastModifiedBy("IBS CJDW")
        ->setTitle("Stock Report")
        ->setSubject("")
        ->setDescription("Stock Report")
        ->setKeywords("Stock, Report")
        ->setCategory("Stock Report");
        
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
             $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
             $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
             $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(5);
             $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
             $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
             $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
             $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);

             
             
             $spreadsheet->getActiveSheet()->mergeCells("C1:H1");
             $spreadsheet->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
             $spreadsheet->getActiveSheet()->mergeCells("C3:H3");
             $spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
             $spreadsheet->getActiveSheet()->mergeCells("C4:H4");
             $spreadsheet->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
             $spreadsheet->getActiveSheet()->mergeCells("C5:H5");
             $spreadsheet->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
             $spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setBold(true)->setSize(16);
             $spreadsheet->getActiveSheet()->getStyle('C5:H5')->getFont()->setBold(true);
             $spreadsheet->getActiveSheet()->getStyle('B5:h5')->getFill();
             // ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
             // ->getStartColor()->setARGB('FAFAFA');
            //  $spreadsheet->getActiveSheet()->getStyle('D7:H7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            //  $spreadsheet->getActiveSheet()->getStyle('D7:H7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
             $j=8;
             // $spreadsheet->getActiveSheet()->mergeCells("D".$j.":H".$j);
             $sheet->setCellValue('C1',"Laporan Perbandingan");
                $sheet->setCellValue('C3',"Tanggal  : ".date('d-m-Y')." ".date('H:i'));
                $sheet->setCellValue('C4',"Dicetak  : ".Auth::user()->name);

        foreach ($data2 as $rows) {
            $sheet->setCellValue('C'.$j,"No Penggunaan"); 
            $sheet->setCellValue('D'.$j, $rows->transfer_no); 
            $j++;

            $sheet->setCellValue('C'.$j,"Tanggal"); 
            $sheet->setCellValue('D'.$j, $rows->transfer_date); 
            $j++;

            $sheet->setCellValue('C'.$j,"Keterangan"); 
            $sheet->setCellValue('D'.$j, $rows->transfer_remark); 
            $j++;
                
                $spreadsheet->getActiveSheet()->mergeCells("D".$j.":H".$j);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('D'.$j,"Bahan Baku");

            $j++;
            
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('D'.$j, "No");
                $sheet->setCellValue('E'.$j,"Nama Kategori");
                $sheet->setCellValue('F'.$j,"Nama Barang");
                $sheet->setCellValue('G'.$j,"Nama Satuan");
                $sheet->setCellValue('H'.$j,"Jumlah Beli Barang");
            
            $j++;
            
            $dataItem = collect(json_decode($data->where('stock_transfer_id',$rows->stock_transfer_id)->pluck('transfer_data')[0]));
            
            $no=0;
            foreach ($dataItem as $val) {
                if ($val->type == 'ingredient'){
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Perbandingan");
                         $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                         $spreadsheet->getActiveSheet()->getStyle('H'.$j.':H'.$j)->getNumberFormat()->setFormatCode('0.00');
                         $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                         $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                         $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                         $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                         $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                         
                         
                         $no++;
                         $sheet->setCellValue('D'.$j, $no);
                         $sheet->setCellValue('E'.$j, $this->getCategoryName($val->item_category_id));
                         $sheet->setCellValue('F'.$j, $this->getItemName($val->item_id));
                         $sheet->setCellValue('G'.$j, $this->getItemUnitName($val->item_unit_id));
                         $sheet->setCellValue('H'.$j, $val->quantity);
                         
                         $j++;
                        }
                    }
                $spreadsheet->getActiveSheet()->mergeCells("D".$j.":H".$j);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $j++;


            $spreadsheet->getActiveSheet()->mergeCells("D".$j.":H".$j);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('D'.$j,"Menu Jadi");
        $j++;
                
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('D'.$j, "No");
                $sheet->setCellValue('E'.$j,"Nama Kategori");
                $sheet->setCellValue('F'.$j,"Nama Barang");
                $sheet->setCellValue('G'.$j,"Nama Satuan");
                $sheet->setCellValue('H'.$j,"Jumlah Beli Barang");
            
            $j++;
            $no=0;
            foreach ($dataItem as $val) {
                if ($val->type == 'menu'){

            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Laporan Perbandingan");
            $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j.':H'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('D'.$j, "No");
            $sheet->setCellValue('E'.$j,"Nama Kategori");
            $sheet->setCellValue('F'.$j,"Nama Barang");
            $sheet->setCellValue('G'.$j,"Nama Satuan");
            $sheet->setCellValue('H'.$j,"Jumlah Beli Barang");
            
            $no++;
            $sheet->setCellValue('D'.$j, $no);
            $sheet->setCellValue('E'.$j, $this->getCategoryName($val->item_category_id));
            $sheet->setCellValue('F'.$j, $this->getItemName($val->item_id));
            $sheet->setCellValue('G'.$j, $this->getItemUnitName($val->item_unit_id));
            $sheet->setCellValue('H'.$j, $val->quantity);
            $j++;
        }
    }
    
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getActiveSheet()->mergeCells("D".$j.":H".$j);
        $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
        $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j);
        $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $j++;

    //    $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getFont()->setBold(true);
    //    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
    //    $spreadsheet->getActiveSheet()->getStyle('D'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    //    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
       
    //    $j++;

    }
       
       $spreadsheet->getActiveSheet()->mergeCells('D'.$j.':H'.$j);
       $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
       $sheet->setCellValue('D'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
             
             $filename='Laporan_Stok.xls';
             header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
             header('Content-Disposition: attachment;filename="'.$filename.'"');
             header('Cache-Control: max-age=0');
             
             $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
             $writer->save('php://output');
        
        }
}
