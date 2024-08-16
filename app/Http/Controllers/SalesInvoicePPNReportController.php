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

class SalesInvoicePPNReportController extends Controller
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

        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice.data_state',0)
        ->get();
        
        return view('content.SalesInvoicePPNReport.ListSalesInvoicePPNReport', compact('data','start_date','end_date'));
    }

    public function filterSalesInvoicePPNReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/sales-invoice-ppn-report');
    }

    public function filterResetSalesInvoicePPNReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-invoice-ppn-report');
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

    public function printSalesInvoicePPNReport()
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PPN</div></td>
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
                <td width=\"8%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"23%\"><div style=\"text-align: center;\">No. Invoice</div></td>
                <td width=\"23%\"><div style=\"text-align: center;\">Tanggal</div></td>
                <td width=\"23%\"><div style=\"text-align: center;\">Persentase PPN</div></td>
                <td width=\"23%\"><div style=\"text-align: center;\">PPN</div></td>
            </tr>
        ";

        $no         = 1;
        $total      = 0;
        $tblStock2  = " ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:center\">".$val['sales_invoice_no']."</td>
                    <td style=\"text-align:center\">".$val['sales_invoice_date']."</td>
                    <td style=\"text-align:right\">".number_format($val['ppn_percentage'], 2)."</td>
                    <td style=\"text-align:right\">".number_format($val['ppn_amount'], 2)."</td>
                </tr>
            ";
            $no++;
            $total += $val['ppn_amount'];
        }

        $tblStock3 = " 
            <tr>			
                <td style=\"text-align:center; font-weight:bold\" colspan=\"4\">Total</td>
                <td style=\"text-align:right; font-weight:bold\">".number_format($total, 2)."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Penjualan_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoicePPNReport()
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

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan PPN")
                                        ->setSubject("")
                                        ->setDescription("Laporan PPN")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan PPN");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan PPN Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"No. Invoice");
            $sheet->setCellValue('D3',"Tanggal");
            $sheet->setCellValue('E3',"Persentase PPN");
            $sheet->setCellValue('F3',"PPN");
            
            $j      = 4;
            $no     = 0;
            $total  = 0;
            
            foreach($data as $key=>$val){
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan PPN");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.($j+1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $no++;
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, $val['sales_invoice_no']);
                $sheet->setCellValue('D'.$j, $val['sales_invoice_date']);
                $sheet->setCellValue('E'.$j, $val['ppn_percentage']);
                $sheet->setCellValue('F'.$j, $val['ppn_amount']);

                $j++;
                $total += $val['ppn_amount'];
            }

            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);

            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('B'.$j, "TOTAL");
            $sheet->setCellValue('F'.$j, $total);

            
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
