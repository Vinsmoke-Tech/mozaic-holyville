<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\InvtItemCategory;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PurchaseInvoiceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        if(!Session::get('category_id')){
            $category_id     = '';
        }else{
            $category_id = Session::get('category_id');
        }

        
        if( $category_id == ''){
            $data = PurchaseInvoice::
            join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','=','purchase_invoice_item.purchase_invoice_id')
            -> where('purchase_invoice.purchase_invoice_date','>=',$start_date)
            ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
            ->where('purchase_invoice.purchase_method', 1)
            ->where('purchase_invoice.company_id', Auth::user()->company_id)
            ->where('purchase_invoice.data_state',0)
            ->get();
            }else{
                $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','=','purchase_invoice_item.purchase_invoice_id')
            ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
            ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
            ->where('purchase_invoice.purchase_method', 1)
            ->where('purchase_invoice_item.item_category_id', $category_id)
            ->where('purchase_invoice.company_id', Auth::user()->company_id)
            ->where('purchase_invoice.data_state',0)
            ->get();
        }
    
            $category = InvtItemCategory::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name','item_category_id');
            
        return view('content.PurchaseInvoiceReport.ListPurchaseInvoiceReport', compact('data','category','start_date','end_date','category_id'));
    }

    public function filterPurchaseInvoiceReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $category_id = $request->category_id;
        
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('category_id', $category_id);

        return redirect('/purchase-invoice-report');
    }

    public function filterResetPurchaseInvoiceReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('category_id');
        return redirect('/purchase-invoice-report');
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id',$item_category_id)->first();

        return $data['item_category_name'];
    }

    public function getUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id',$item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function printPurchaseInvoiceReport()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        if(!Session::get('warehouse_id')){
            $warehouse_id     = '';
        }else{
            $warehouse_id = Session::get('warehouse_id');
        }

        $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.warehouse_id', $warehouse_id)
        ->where('purchase_invoice.purchase_method', 1)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->where('purchase_invoice.data_state',0)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PEMBELIAN</div></td>
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
                <td width=\"3%\" ><div style=\"text-align: center;\">No</div></td>
                <td width=\"15%\" ><div style=\"text-align: center;\">Nama Pemasok</div></td>
                <td width=\"15%\" ><div style=\"text-align: center;\">Nama Gudang</div></td>
                <td width=\"13%\" ><div style=\"text-align: center;\">Nama Barang</div></td>
                <td width=\"10%\" ><div style=\"text-align: center;\">Tanggal Pembelian</div></td>
                <td width=\"10%\" ><div style=\"text-align: center;\">Quantity</div></td>
                <td width=\"10%\" ><div style=\"text-align: center;\">Satuan</div></td>
                <td width=\"10%\" ><div style=\"text-align: center;\">Harga/Satuan</div></td>
                <td width=\"15%\" ><div style=\"text-align: center;\">Jumlah Total</div></td>
            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:left\">$no.</td>
                    <td style=\"text-align:left\">".$val['purchase_invoice_supplier']."</td>
                    <td style=\"text-align:left\">".$this->getWarehouseName($val['warehouse_id'])."</td>
                    <td style=\"text-align:left\">".$this->getItemName($val['item_id'])."</td>
                    <td style=\"text-align:left\">".$val['purchase_invoice_date']."</td>
                    <td style=\"text-align:left\">".$val['quantity']."</td>
                    <td style=\"text-align:left\">".$this->getUnitName($val['item_unit_id'])."</td>
                    <td style=\"text-align:right\">".number_format($val['item_unit_cost'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['total_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
        }
        $tblStock3 = " 

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Pembelian_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportPurchaseInvoiceReport()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        if(!Session::get('category_id')){
            $category_id     = '';
        }else{
            $category_id = Session::get('category_id');
        }

        if( $category_id == ''){
            $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','=','purchase_invoice_item.purchase_invoice_id')
            ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
            ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
            ->where('purchase_invoice.purchase_method', 1)
            ->where('purchase_invoice.company_id', Auth::user()->company_id)
            ->where('purchase_invoice.data_state',0)
            ->get();
            }else{
                $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','=','purchase_invoice_item.purchase_invoice_id')
            ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
            ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
            ->where('purchase_invoice.purchase_method', 1)
            ->where('purchase_invoice_item.item_category_id', $category_id)
            ->where('purchase_invoice.company_id', Auth::user()->company_id)
            ->where('purchase_invoice.data_state',0)
            ->get();
            }
        
        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("IBS CJDW")
                                        ->setLastModifiedBy("IBS CJDW")
                                        ->setTitle("Purchase Invoice Report")
                                        ->setSubject("")
                                        ->setDescription("Purchase Invoice Report")
                                        ->setKeywords("Purchase, Invoice, Report")
                                        ->setCategory("Purchase Invoice Report");
                                
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:J1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:J3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pembelian Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Pemasok");
            $sheet->setCellValue('D3',"Nama Gudang");
            $sheet->setCellValue('E3',"Nama Barang");
            $sheet->setCellValue('F3',"Tanggal Pembelian");
            $sheet->setCellValue('G3',"Quantity");
            $sheet->setCellValue('H3',"Satuan");
            $sheet->setCellValue('I3',"Harga Per Satuan");
            $sheet->setCellValue('J3',"Subtotal"); 
            
            $j=4;
            $no=0;
            $subtotal_amount = 0;

            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Jurnal Umum");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':J'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('H'.$j.':J'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                    $no++;
                    $sheet->setCellValue('B'.$j, $no);
                    $sheet->setCellValue('C'.$j, $val['purchase_invoice_supplier']);
                    $sheet->setCellValue('D'.$j, $this->getCategoryName($val['item_category_id']));
                    $sheet->setCellValue('E'.$j, $this->getItemName($val['item_id']));
                    $sheet->setCellValue('F'.$j, $val['purchase_invoice_date']);
                    $sheet->setCellValue('G'.$j, $val['quantity']);
                    $sheet->setCellValue('H'.$j, $this->getUnitName($val['item_unit_id']));
                    $sheet->setCellValue('I'.$j, number_format($val['item_unit_cost'],2,'.',','));
                    $sheet->setCellValue('J'.$j, number_format($val['subtotal_amount'],2,'.',','));
                }
                $subtotal_amount += $val['subtotal_amount'];

                $j++;
        
            }

            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':J'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':I'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':J'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$j.':L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            $sheet->setCellValue('B'.$j,'Total');
            $sheet->setCellValue('J'.$j, number_format($subtotal_amount,2,'.',','));
            
            // ob_clean();
            $filename='Laporan_Pembelian_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}
