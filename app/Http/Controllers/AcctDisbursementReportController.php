<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\Expenditure;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctDisbursementReportController extends Controller
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

        $data = Expenditure::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();
        
        return view('content.AcctDisbursementReport.ListAcctDisbursementReport', compact('data','start_date','end_date'));
    }

    public function filterDisbursementReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/cash-disbursement-report');
    }

    public function resetFilterDisbursementReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/cash-disbursement-report');
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

    public function getAccountStatus($account_setting_name, $company_id)
    {
        $data = AcctAccountSetting::where('company_id', $company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name, $company_id)
    {
        $data = AcctAccountSetting::where('company_id', $company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)
        ->first();

        return $data['account_default_status'];
    }

    public function deleteDisbursementReport($expenditure_id){
        $expenditure = Expenditure::findOrFail($expenditure_id);
        if($expenditure['expenditure_date'] < '2023-03-13'){
            $msg = 'Pengeluaran dibawah tanggal 13 Maret 2023 tidak bisa dihapus!';
            return redirect('/cash-disbursement-report')->with('msg',$msg);
        }
        $expenditure->data_state = 1;
        $expenditure->deleted_at = date('Y-m-d');
        $expenditure->deleted_id = Auth::id();
        if($expenditure->save()){
            $transaction_module_code    = 'HPGL';
            $transaction_module_id      = $this->getTransactionModuleID($transaction_module_code);
            
            $journal = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_status'        => 1,
                'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                'transaction_module_id'         => $transaction_module_id,
                'transaction_module_code'       => $transaction_module_code,
                'journal_voucher_date'          => date('Y-m-d'),
                'journal_voucher_period'        => date('Ym'),
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );

            if(JournalVoucher::create($journal)){
                $journal_voucher_id = JournalVoucher::where('company_id', Auth::user()->company_id)
                ->orderBy('created_at', 'DESC')
                ->first();

                $account_setting_name   = 'expenditure_cash_account';
                $account_id             = $this->getAccountId($account_setting_name, Auth::user()->company_id);
                $account_status         = 0;
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                if($account_status      == 0){
                    $debit_ammount     = $expenditure['expenditure_amount'];
                    $credit_ammount      = 0;
                }else{
                    $credit_ammount     = $expenditure['expenditure_amount'];
                    $debit_ammount      = 0;
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $expenditure['expenditure_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_status,
                    'journal_voucher_debit_amount'  => $debit_ammount,
                    'journal_voucher_credit_amount' => $credit_ammount,
                    'updated_id'                    => Auth::id(),
                    'created_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);

                $account_id = $expenditure['account_id'];
                $account_status = 1;
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                if($account_status == 0){
                    $debit_ammount     = $expenditure['expenditure_amount'];
                    $credit_ammount      = 0;
                }else{
                    $credit_ammount     = $expenditure['expenditure_amount'];
                    $debit_ammount      = 0;
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $expenditure['expenditure_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_status,
                    'journal_voucher_debit_amount'  => $debit_ammount,
                    'journal_voucher_credit_amount' => $credit_ammount,
                    'updated_id'                    => Auth::id(),
                    'created_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
            }
            
            $msg = 'Hapus Pengeluaran Kas Berhasil';
            return redirect('/cash-disbursement-report')->with('msg',$msg);
        }else{
            $msg = 'Hapus Pengeluaran Kas Gagal';
            return redirect('/cash-disbursement-report')->with('msg',$msg);
        }
    }

    public function printDisbursementReport()
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

        $data = Expenditure::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENGELUARAN KAS</div></td>
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
                <td width=\"20%\" ><div style=\"text-align: center;\">Keterangan</div></td>
                <td width=\"15%\" ><div style=\"text-align: center;\">Tanggal</div></td>
                <td width=\"13%\" ><div style=\"text-align: center;\">Nominal</div></td>
            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['expenditure_remark']."</td>
                    <td style=\"text-align:left\">".$val['expenditure_date']."</td>
                    <td style=\"text-align:right\">".number_format($val['expenditure_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
        }
        $tblStock3 = " 

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        // ob_clean();

        $filename = 'Laporan_Pengeluaran_kas_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportDisbursementReport()
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

        $data = Expenditure::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Cash Disbursement Report")
                                        ->setSubject("")
                                        ->setDescription("Cash Disbursement Report")
                                        ->setKeywords("Cash, Disbursement, Report")
                                        ->setCategory("Cash Disbursement Report");

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

            $sheet->setCellValue('B1',"Laporan Pengeluaran Kas Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Keterangan");
            $sheet->setCellValue('D3',"Tanggal");
            $sheet->setCellValue('E3',"Nominal"); 
            
            $j=4;
            $no=0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Pengeluaran Kas");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('H'.$j.':E'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                    $no++;
                    $sheet->setCellValue('B'.$j, $no);
                    $sheet->setCellValue('C'.$j, $val['expenditure_remark']);
                    $sheet->setCellValue('D'.$j, $val['expenditure_date']);
                    $sheet->setCellValue('E'.$j, number_format($val['expenditure_amount'],2,'.',','));
                }
                $j++;
        
            }
            
            // ob_clean();
            $filename='Laporan_Pengeluaran_Kas_'.$start_date.'_s.d._'.$end_date.'.xls';
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
