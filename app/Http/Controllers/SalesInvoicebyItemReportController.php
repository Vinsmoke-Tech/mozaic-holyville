<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesInvoicebyItemReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        // set_time_limit(3000);

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
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get();
    

        $category = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');

        return view('content.SalesInvoicebyItemReport.ListSalesInvoicebyItemReport',compact('category', 'data','start_date','end_date','category_id'));
    }

    public function filterSalesInvoicebyItemReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $category_id = $request->category_id;


        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);
        Session::put('category_id', $category_id);


        return redirect('/sales-invoice-by-item-report');
    }

    public function filterResetSalesInvoicebyItemReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('category_id');


        return redirect('/sales-invoice-by-item-report');
    }

    public function getItemName($item_id)
    {
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }
        
        if( $category_id == ''){
            $data   = InvtItem::where('item_id', $item_id)
            ->first();
        }else{
            $data   = InvtItem::where('item_id', $item_id)
            // ->where('item_category_id',$category_id)
            ->first();
        }

        return $data['item_name'];
    }

    public function getCategoryName($item_category_id)
    {
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        if( $category_id == ''){
            $data   = InvtItemCategory::where('item_category_id',$item_category_id)->first();
        }else{
            $data = InvtItemCategory::where('item_category_id', $category_id)
            ->first();
        }

        return $data['item_category_name'];
    }

    public function getTotalItem($item_id)
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
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        if( $category_id == ''){
        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice_item.quantity','>=',1)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.item_id', $item_id)
            ->where('sales_invoice.data_state',0)
            ->get();
        }else{
            $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice_item.quantity','>=',1)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.item_category_id',$category_id)
            ->where('sales_invoice_item.item_id', $item_id)
            ->where('sales_invoice.data_state',0)
            ->get();
        }
        // echo json_encode($data); exit;
        $total_item = 0;
        foreach ($data as $key => $val) {
            $data_packge[$key] = SalesInvoiceItem::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->where('item_id', $val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->where('quantity', '>=',1)
            ->first();
            $total_item += $val['quantity'];
        }

        return $total_item;
    }

    public function printSalesInvoicebyItemReport()
    {
        // set_time_limit(3000);

        if(!$start_date = Session::get('start_date')){
            $start_date = '';
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = '';
        } else {
            $end_date = Session::get('end_date');
        }
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        if( $category_id == ''){
            // $data = SalesInvoiceItem::join('invt_item','invt_item.item_id','sales_invoice_item.item_id') 
            //         ->where('sales_invoice_item.quantity','>',0)
            //         ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            //         ->get();
            
            $data = InvtItem::join('sales_invoice_item','sales_invoice_item.item_id','invt_item.item_id')
            ->where('sales_invoice_item.data_state',0)
            ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.quantity','>=',1)
            ->groupBy('sales_invoice_item.item_id')
            ->get();
        }else{

            // $data = SalesInvoiceItem::join('invt_item','invt_item.item_id','sales_invoice_item.item_id') 
            //         ->where('sales_invoice_item.quantity','>',0)
            //         ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            //         ->where('sales_invoice_item.item_category_id', $category_id)
            //         ->get();
            $data = InvtItem::join('sales_invoice_item','sales_invoice_item.item_id','invt_item.item_id')
            ->where('sales_invoice_item.data_state',0)
            ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.quantity','>=',1)
            ->where('sales_invoice_item.item_category_id', $category_id)
            ->groupBy('sales_invoice_item.item_id')
            ->get();
       
        }


        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN BARANG</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"15%\"><div style=\"text-align: center;\">Kategori Barang</div></td>
                <td width=\"15%\"><div style=\"text-align: center;\">Nama Barang</div></td>
                <td width=\"10%\"><div style=\"text-align: center;\">Jumlah Penjualan</div></td>

            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $id = $val['sales_invoice_id'];

            if($val['sales_invoice_id'] == $id){
                $totalitem = $this->getTotalItem($val['item_id']);
                        if($totalitem >= 1){
                        $tblStock2 .="
                            <tr>			
                                <td style=\"text-align:center\">$no.</td>
                                <td style=\"text-align:left\">".$this->getCategoryName($val['item_category_id'])."</td>
                                <td style=\"text-align:left\">".$this->getItemName($val['item_id'])."</td>
                                <td style=\"text-align:left\">".$this->getTotalItem($val['item_id'])."</td>
                            </tr>
                            
                        ";
                        $no++;
                        }else{

                        }
            }
        }
        $tblStock3 = " 

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Penjualan_Barang_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoicebyItemReport()
    {
        // set_time_limit(3000);

        // $data = InvtItem::join('sales_invoice_item','sales_invoice_item.item_id','invt_item.item_id')
        // ->where('sales_invoice_item.data_state',0)
        // ->where('sales_invoice_item.company_id', Auth::user()->company_id)
        // ->where('sales_invoice_item.quantity','>',0)
        // ->groupBy('sales_invoice_item.item_id')
        // ->get();
        // dd($data);

        if(!$start_date = Session::get('start_date')){
            $start_date = '';
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = '';
        } else {
            $end_date = Session::get('end_date');
        }
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        if( $category_id == ''){
            // $data = SalesInvoiceItem::join('invt_item','invt_item.item_id','sales_invoice_item.item_id') 
            //         ->where('sales_invoice_item.quantity','>',0)
            //         ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            //         ->get();
            
            $data = InvtItem::join('sales_invoice_item','sales_invoice_item.item_id','invt_item.item_id')
            ->where('sales_invoice_item.data_state',0)
            ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.quantity','>=',1)
            ->groupBy('sales_invoice_item.item_id')
            ->get();
            // dd($data);
        }else{

        //     // $data = SalesInvoiceItem::join('invt_item','invt_item.item_id','sales_invoice_item.item_id') 
        //     //         ->where('sales_invoice_item.quantity','>',0)
        //     //         ->where('sales_invoice_item.company_id', Auth::user()->company_id)
        //     //         ->where('sales_invoice_item.item_category_id', $category_id)
        //     //         ->get();
            $data = InvtItem::join('sales_invoice_item','sales_invoice_item.item_id','invt_item.item_id')
            ->where('sales_invoice_item.data_state',0)
            ->where('sales_invoice_item.company_id', Auth::user()->company_id)
            ->where('sales_invoice_item.quantity','>=',1)
            ->where('sales_invoice_item.item_category_id', $category_id)
            ->groupBy('sales_invoice_item.item_id')
            ->get();
       
        }

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Laporan Penjualan")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan Penjualan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:E1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan Barang Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Kategori");
            $sheet->setCellValue('D3',"Nama Barang");
            $sheet->setCellValue('E3',"Jumlah Penjualan");
            
            $j=4;
            $no=0;
            $subtotal_amount=0;         
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan Barang");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('H'.$j.':E'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                    $id = $val['sales_invoice_id'];

                    if($val['sales_invoice_id'] == $id){

                        $totalitem = $this->getTotalItem($val['item_id']);
                        if($totalitem >= 1){
                            $no++;
                            $sheet->setCellValue('B'.$j, $no);
                            $sheet->setCellValue('C'.$j, $this->getCategoryName($val['item_category_id']));
                            $sheet->setCellValue('D'.$j, $this->getItemName($val['item_id']));
                            $sheet->setCellValue('E'.$j, $totalitem);
                        }else{
                            // Delete the row when totalitem is less than 1
                                $sheet->removeRow($j);
                                
                            // Optionally, you might want to decrement $j to stay on the current row after deletion
                                $j--;
    
                        }
                    }
                    
                }else{
                    // continue;
                }
                $j++;
                $subtotal_amount += $this->getTotalItem($val['item_id']);
                
            }
            
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            
            $sheet->setCellValue('B'.$j,'Total Barang Terjual');
            $sheet->setCellValue('E'.$j, number_format($subtotal_amount,2,'.',','));

            // ob_clean();
            $filename='Laporan_Penjualan_Barang_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }

    public function notSold()
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
        $data_item = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get(); 

        if (empty($data_item)){
            $coba1 = "kosong";
        } else {
            foreach ($data_item as $key => $val) {
                $coba1[$key] = $val['item_id'];
            }
        }
        if (empty($coba1)){
            $data = [];
        }else{
            $data = InvtItem::whereNotIn('item_id', $coba1)->get();
        }
        return view('content.SalesInvoicebyItemReport.ListSalesInvoicebyItemNotSoldReport',compact('data','start_date','end_date'));
    }
    
    public function filterSalesInvoicebyItemNotSoldReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/sales-invoice-by-item-report/not-sold');
    }

    public function filterResetSalesInvoicebyItemNotSoldReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-invoice-by-item-report/not-sold');
    }
    public function printSalesInvoicebyItemNotSoldReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = '';
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = '';
        } else {
            $end_date = Session::get('end_date');
        }
        $data_item = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get(); 

        if (empty($data_item)){
            $coba1 = "kosong";
        } else {
            foreach ($data_item as $key => $val) {
                $coba1[$key] = $val['item_id'];
            }
        }
        if (empty($coba1)){
            $data = [];
        }else{
            $data = InvtItem::whereNotIn('item_id', $coba1)->get();
        }

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN BARANG YANG TIDAK TERJUAL</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Kategori Barang</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Barang</div></td>
            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $id = $val['purchase_retur_id'];

            if($val['purchase_retur_id'] == $id){
                $tblStock2 .="
                    <tr>			
                        <td style=\"text-align:center\">$no.</td>
                        <td style=\"text-align:left\">".$this->getCategoryName($val['item_category_id'])."</td>
                        <td style=\"text-align:left\">".$this->getItemName($val['item_id'])."</td>
                    </tr>
                    
                ";
                $no++;
            }
        }
        $tblStock3 = " 

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Penjualan_Barang_Yang_Tidak_Terjual_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoicebyItemNotSoldReport()
    {
        
        if(!$start_date = Session::get('start_date')){
            $start_date = '';
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = '';
        } else {
            $end_date = Session::get('end_date');
        }
        $data_item = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get(); 

        if (empty($data_item)){
            $coba1 = "kosong";
        } else {
            foreach ($data_item as $key => $val) {
                $coba1[$key] = $val['item_id'];
            }
        }
        if (empty($coba1)){
            $data = [];
        }else{
            $data = InvtItem::whereNotIn('item_id', $coba1)->get();
        }

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Laporan Penjualan")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan Penjualan");
                                
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:D1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:D3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan Barang Yang Tidak Terjual Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Kategori");
            $sheet->setCellValue('D3',"Nama Barang");
            
            $j=4;
            $no=0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan Barang");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':D'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('H'.$j.':D'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);



                    $id = $val['sales_invoice_id'];

                    if($val['sales_invoice_id'] == $id){

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getCategoryName($val['item_category_id']));
                        $sheet->setCellValue('D'.$j, $this->getItemName($val['item_id']));

                    }
                        
                    
                }else{
                    continue;
                }
                $j++;
        
            }
            
            // ob_clean();
            $filename='Laporan_Penjualan_Barang_Yang_Tidak_Terjual_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }

    public function tableSalesInvoiceByItem(Request $request)
    {
        if(!Session::get('category_id')){
            $category_id = '';
        } else {
            $category_id = Session::get('category_id');
        }

        $draw 				= $request->get('draw');
        $start 				= $request->get("start");
        $rowPerPage 		= $request->get("length");
        $orderArray 	    = $request->get('order');
        $columnNameArray 	= $request->get('columns');
        $searchArray 		= $request->get('search');
        $columnIndex 		= $orderArray[0]['column'];
        $columnName 		= $columnNameArray[$columnIndex]['data'];
        $columnSortOrder 	= $orderArray[0]['dir'];
        $searchValue 		= $searchArray['value'];
        $valueArray         = explode (" ",$searchValue);


        $users = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        $total = $users->count();

        if( $category_id == ''){
            $totalFilter = InvtItem::where('data_state','=',0)
            ->where('company_id', Auth::user()->company_id);
    
            if (!empty($searchValue)) {
                if (count($valueArray) != 1) {
                    foreach ($valueArray as $key => $val) {
                        $totalFilter = $totalFilter->where('item_name','like','%'.$val.'%');
                    }
                } else {
                    $totalFilter = $totalFilter->where('item_name','like','%'.$searchValue.'%');
                }
            }
        }else{
            $totalFilter = InvtItem::where('data_state','=',0)
            ->where('company_id', Auth::user()->company_id)
            ->where('item_category_id', $category_id);
    
            if (!empty($searchValue)) {
                if (count($valueArray) != 1) {
                    foreach ($valueArray as $key => $val) {
                        $totalFilter = $totalFilter->where('item_name','like','%'.$val.'%');
                    }
                } else {
                    $totalFilter = $totalFilter->where('item_name','like','%'.$searchValue.'%');
                }
            }
        }

        $totalFilter = $totalFilter->count();

        if( $category_id == ''){
            $arrData = InvtItem::where('data_state','=',0)
            ->where('company_id', Auth::user()->company_id);
            $arrData = $arrData->skip($start)->take($rowPerPage);
            $arrData = $arrData->orderBy($columnName,$columnSortOrder);
            }else{
                $arrData = InvtItem::where('data_state','=',0)
                ->where('company_id', Auth::user()->company_id)
                ->where('item_category_id', $category_id);
                $arrData = $arrData->skip($start)->take($rowPerPage);
                $arrData = $arrData->orderBy($columnName,$columnSortOrder);
            }

        if (!empty($searchValue)) {
            if (count($valueArray) != 1) {
                foreach ($valueArray as $key => $val) {
                    $arrData = $arrData->where('item_name','like','%'.$val.'%');
                }
            } else {
                $arrData = $arrData->where('item_name','like','%'.$searchValue.'%');
            }
        }

        $arrData = $arrData->get();

        $no    = $start;
        $data   = array();
        foreach ($arrData as $key => $val) {
            $no++;
            $row = array();
            $row['no']                  = "<div class='text-center'>".$no.".</div>";
            $row['item_category_name']  = $this->getCategoryName($val['item_category_id']);
            $row['item_name']           = $this->getItemName($val['item_id']); 
            $row['total_item']          = "<div class='text-right'>".$this->getTotalItem($val['item_id'])."</div>";

            $data[] = $row;
        }
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFilter,
            "data" => $data,
        );

        return json_encode($response);
    }
}
