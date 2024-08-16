<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class SalesInvoiceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
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
        if(!Session::get('item_category_id')){
            $item_category_id = '';
        } else {
            $item_category_id = Session::get('item_category_id');
        }

        if( $item_category_id == ''){
            $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice.data_state',0)
            ->get();
        }else{
            $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice_item.item_category_id', $item_category_id)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice.data_state',0)
            ->get();
        }

        // echo json_encode($data);exit;
        $category_id = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');

        return view('content.SalesInvoiceReport.ListSalesInvoiceReport', compact('data','start_date','end_date','category_id','item_category_id'));
    }

    public function filterSalesInvoiceReport(Request $request)
    {
        $start_date         = $request->start_date;
        $end_date           = $request->end_date;
        $item_category_id   = $request->item_category_id;


        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);
        Session::put('item_category_id', $item_category_id);

        return redirect('/sales-invoice-report');
    }

    public function filterResetSalesInvoiceReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('item_category_id');

        return redirect('/sales-invoice-report');
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id', $item_category_id)->first();

        return $data['item_category_name'];
    }

    public function printSalesInvoiceReport()
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
        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get();
        
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10);

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage('L', 'A4');

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no         = 1;
        $tblStock1  = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"2%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"8%\"><div style=\"text-align: center;\">Tanggal</div></td>
                <td width=\"10%\"><div style=\"text-align: center;\">No. Invoice</div></td>
                <td width=\"10%\"><div style=\"text-align: center;\">Kategori Barang</div></td>
                <td width=\"10%\"><div style=\"text-align: center;\">Nama Barang</div></td>
                <td width=\"10%\"><div style=\"text-align: center;\">Satuan</div></td>
                <td width=\"5%\"><div style=\"text-align: center;\">Qty</div></td>
                <td width=\"9%\"><div style=\"text-align: center;\">Harga</div></td>
                <td width=\"9%\" ><div style=\"text-align: center;\">Subtotal</div></td>
                <td width=\"9%\"><div style=\"text-align: center;\">Diskon</div></td>
                <td width=\"9%\"><div style=\"text-align: center;\">PPN</div></td>
                <td width=\"9%\"><div style=\"text-align: center;\">Total</div></td>
            </tr>";

        $no         = 1;
        $tblStock2  = " ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:center\">".$val['sales_invoice_date']."</td>
                    <td style=\"text-align:center\">".$val['sales_invoice_no']."</td>
                    <td style=\"text-align:center\">".$this->getCategoryName($val['item_category_id'])."</td>
                    <td style=\"text-align:center\">".$this->getItemName($val['item_id'])."</td>
                    <td style=\"text-align:center\">".$this->getItemUnitName($val['item_unit_id'])."</td>
                    <td style=\"text-align:center\">".$val['quantity']."</td>
                    <td style=\"text-align:right\">".number_format($val['item_unit_price'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['subtotal_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['discount_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['ppn_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format(($val['subtotal_amount']-$val['discount_amount']+$val['ppn_amount']),2,'.',',')."</td>
                </tr>
            ";
            $no++;
        }

        $tblStock3 = " 
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Penjualan_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoiceReport()
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
        if(!Session::get('item_category_id')){
            $item_category_id = '';
        } else {
            $item_category_id = Session::get('item_category_id');
        }

        if( $item_category_id == ''){
            $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice_item.quantity','>',0)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice.data_state',0)
            ->get();
        }else{
            $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
            ->where('sales_invoice.sales_invoice_date','>=',$start_date)
            ->where('sales_invoice.sales_invoice_date','<=',$end_date)
            ->where('sales_invoice_item.quantity','>',0)
            ->where('sales_invoice_item.item_category_id', $item_category_id)
            ->where('sales_invoice.company_id', Auth::user()->company_id)
            ->where('sales_invoice.data_state',0)
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
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:M1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:M3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:M3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Tanggal");
            $sheet->setCellValue('D3',"No. Invoice");
            $sheet->setCellValue('E3',"Kategori Barang");
            $sheet->setCellValue('F3',"Nama Barang");
            $sheet->setCellValue('G3',"Satuan");
            $sheet->setCellValue('H3',"Qty");
            $sheet->setCellValue('I3',"Harga");
            $sheet->setCellValue('J3',"Subtotal");
            $sheet->setCellValue('K3',"Diskon / Barang");
            $sheet->setCellValue('L3',"PPN / Barang");
            $sheet->setCellValue('M3',"Total");
            
            $j  =   4;
            $no =   0;
            $subtotal_amount = 0;
            $subtotal_amount_ppn = 0;

            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['sales_invoice_date']);
                        $sheet->setCellValue('D'.$j, $val['sales_invoice_no']);
                        $sheet->setCellValue('E'.$j, $this->getCategoryName($val['item_category_id']));
                        $sheet->setCellValue('F'.$j, $this->getItemName($val['item_id']));
                        $sheet->setCellValue('G'.$j, $this->getItemUnitName($val['item_unit_id']));
                        $sheet->setCellValue('H'.$j, $val['quantity']);
                        $sheet->setCellValue('I'.$j, number_format($val['item_unit_price'],2,'.',','));
                        $sheet->setCellValue('J'.$j, number_format($val['subtotal_amount'],2,'.',','));
                        $sheet->setCellValue('K'.$j, number_format($val['discount_amount_total'],2,'.',','));
                        $sheet->setCellValue('L'.$j, number_format($val['ppn_amount'],2,'.',','));
                        $sheet->setCellValue('M'.$j, number_format($val['subtotal_amount']+$val['ppn_amount'],2,'.',','));
                }
                $subtotal_amount        += $val['subtotal_amount'];
                $subtotal_amount_ppn    = $subtotal_amount-$val['discount_amount_total']+$val['ppn_amount'];
                $j++;
        
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.$j.':M'.$j)->getNumberFormat()->setFormatCode('0.00');

            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':I'.$j);
            $spreadsheet->getActiveSheet()->mergeCells('K'.$j.':L'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$j.':L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            $sheet->setCellValue('B'.$j,'Total Sebelum Discount');
            $sheet->setCellValue('J'.$j, number_format($subtotal_amount,2,'.',','));
            $sheet->setCellValue('K'.$j,'Total Sesudah Discount');
            $sheet->setCellValue('M'.$j, number_format($subtotal_amount_ppn,2,'.',','));
            // ob_clean();
            $filename='Laporan_Penjualan_'.$start_date.'_s.d._'.$end_date.'.xls';
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
