<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctProfitDayReport;
use App\Models\JournalVoucher;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctProfitDayReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('d');
        } else {
            $end_date = Session::get('end_date');
        }  


        $data = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher_item.account_id',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->whereIn('acct_journal_voucher_item.account_id',['28','52','46'])
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();
        

        // echo json_encode($data);exit;
        $data2 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher_item.account_id','acct_journal_voucher_item.account_id_status',
        DB::Raw("SUM(CASE WHEN acct_journal_voucher.transaction_module_id = '2' THEN acct_journal_voucher_item.journal_voucher_amount ELSE -acct_journal_voucher_item.journal_voucher_amount END) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher_item.account_id',36)
        ->whereIn('acct_journal_voucher.transaction_module_id', ['2','7'])
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();
        

        $data3 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher_item.account_id', 56)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->get();

        $data4 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher.journal_voucher_description','acct_journal_voucher_item.account_id_status',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher.transaction_module_id', 1)
        ->where('acct_journal_voucher_item.account_id_default_status', 0)
        ->where('acct_journal_voucher_item.account_id_status', 1)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_description')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();

        
        // $count = count($data);
        // echo json_encode($data);exit;
        
        return view('content.AcctProfitDayReport.ListAcctProfitDayReport', compact('start_date','end_date','data','data2','data3','data4'));
    }

    public function getAmountAccount()
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
        

    $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->select('acct_journal_voucher_item.journal_voucher_amount','acct_journal_voucher_item.account_id_status')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->get();

    $data_first = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->select('acct_journal_voucher_item.account_id_status','acct_journal_voucher_item.account_id_default_status')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->first();
    
        
        $amount     = 0;
        $amount1    = 0;
        $amount2    = 0;
        foreach ($data as $key => $val) {

            if ($val['account_id_status'] ==  $data_first['account_id_default_status']) {
                $amount1 += $val['journal_voucher_amount'];
            } else {
                $amount2 += $val['journal_voucher_amount'];

            }
            $amount = $amount1 - $amount2;
        }
        
        return $amount;
    }

    public function filterProfitDayReport(Request $request)
    {
        $start_date             = $request->start_date;
        $end_date               = $request->end_date;


        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/profit-day-report');
    }

    public function resetFilterAcctProfitDayReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('journal_voucher_id');


        return redirect('/profit-day-report');
    }



    public function printAcctProfitDayReport()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        $AcctProfitDayReport_left = AcctProfitDayReport::select('report_tab1','report_bold1','report_type1','account_name1','account_code1','report_no','report_formula1','report_operator1','account_id1')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $AcctProfitDayReport_right = AcctProfitDayReport::select('report_tab2','report_bold2','report_type2','account_name2','account_code2','report_no','report_formula2','report_operator2','account_id2')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
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

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
            <table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
                <tr>
                    <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN NERACA<BR>Periode Januari - ".$this->getMonthName($month)." ".$year."</div></td>
                </tr>
            </table>
        ";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tblHeader = "
        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">			        
            <tr>";
                $tblheader_left = "
                    <td style=\"width: 50%\">	
                        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
                            $tblitem_left = "";
                            $grand_total_account_amount1 = 0;
                            $grand_total_account_amount2 = 0;
                            foreach ($AcctProfitDayReport_left as $keyLeft => $valLeft) {
                                if($valLeft['report_tab1'] == 0){
                                    $report_tab1 = '';
                                } else if($valLeft['report_tab1'] == 1){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;';
                                } else if($valLeft['report_tab1'] == 2){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else if($valLeft['report_tab1'] == 3){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                }

                                if($valLeft['report_bold1'] == 1){
                                    $report_bold1 = 'bold';
                                } else {
                                    $report_bold1 = 'normal';
                                }									

                                if($valLeft['report_type1'] == 1){
                                    $tblitem_left1 = "
                                        <tr>
                                            <td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                        </tr>";
                                } else {
                                    $tblitem_left1 = "";
                                }



                                if($valLeft['report_type1']	== 2){
                                    $tblitem_left2 = "
                                        <tr>
                                            <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                            <td style=\"width: 30%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
                                        </tr>";
                                } else {
                                    $tblitem_left2 = "";
                                }									

                                if($valLeft['report_type1']	== 3){
                                    $last_balance1 	= $this->getAmountAccount($valLeft['account_id1']);		

                                    $tblitem_left3 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
                                        </tr>";

                                    $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                                } else {
                                    $tblitem_left3 = "";
                                }

                                if($valLeft['report_type1']	== 10){
                                    $last_balance10 	= $this->getAmountAccount($valLeft['account_id1']);		


                                    $account_amount10_top[$valLeft['report_no']] = $last_balance10;

                                } else {
                                }
                                

                                if($valLeft['report_type1'] == 11){
                                    if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                        $report_formula1 	= explode('#', $valLeft['report_formula1']);
                                        $report_operator1 	= explode('#', $valLeft['report_operator1']);

                                        $total_account_amount10	= 0;
                                        for($i = 0; $i < count($report_formula1); $i++){
                                            if($report_operator1[$i] == '-'){
                                                if($total_account_amount10 == 0 ){
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount10 = $total_account_amount10 - $account_amount10_top[$report_formula1[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount10 == 0){
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount10;

                                        $tblitem_left10 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount10, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_left10 = "";
                                    }
                                } else {
                                    $tblitem_left10 = "";
                                }

                                if($valLeft['report_type1']	== 7){
                                    $last_balance1 	= $this->getAmountAccount($valLeft['account_id1']);		

                                    $tblitem_left7 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
                                            <td style=\"text-align:right;\">(".number_format($last_balance1, 2).")</td>
                                        </tr>";

                                    $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                                } else {
                                    $tblitem_left7 = "";
                                }
                                

                                if($valLeft['report_type1'] == 5){
                                    if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                        $report_formula1 	= explode('#', $valLeft['report_formula1']);
                                        $report_operator1 	= explode('#', $valLeft['report_operator1']);

                                        $total_account_amount1	= 0;
                                        for($i = 0; $i < count($report_formula1); $i++){
                                            if($report_operator1[$i] == '-'){
                                                if($total_account_amount1 == 0 ){
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount1 == 0){
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount1;

                                        // $tblitem_left5 = "
                                        //     <tr>
                                        //         <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                        //         <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
                                        //     </tr>";
                                        $tblitem_left5 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_left5 = "";
                                    }
                                } else {
                                    $tblitem_left5 = "";
                                }

                                $tblitem_left .= $tblitem_left1.$tblitem_left2.$tblitem_left3.$tblitem_left10.$tblitem_left7.$tblitem_left5;

                                // if($valLeft['report_type1'] == 6){
                                // 	if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                // 		$report_formula1 	= explode('#', $valLeft['report_formula1']);
                                // 		$report_operator1 	= explode('#', $valLeft['report_operator1']);

                                // 		$total_account_amount1	= 0;
                                // 		for($i = 0; $i < count($report_formula1); $i++){
                                // 			if($report_operator1[$i] == '-'){
                                // 				if($total_account_amount1 == 0 ){
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				} else {
                                // 					$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                // 				}
                                // 			} else if($report_operator1[$i] == '+'){
                                // 				if($total_account_amount1 == 0){
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				} else {
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				}
                                // 			}
                                // 		}
                                        
                                // 	} else {
                                        
                                // 	}
                                // } else {
                                    
                                // }

                            }

                $tblfooter_left	= "
                        </table>
                    </td>";

                /* print_r("tblitem_left ");
                print_r($tblitem_left);
                exit; */

                $tblheader_right = "
                    <td style=\"width: 50%\">	
                        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
                            $tblitem_right = "";
                            foreach ($AcctProfitDayReport_right as $keyRight => $valRight) {
                                if($valRight['report_tab2'] == 0){
                                    $report_tab2 = '';
                                } else if($valRight['report_tab2'] == 1){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;';
                                } else if($valRight['report_tab2'] == 2){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else if($valRight['report_tab2'] == 3){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                }

                                if($valRight['report_bold2'] == 1){
                                    $report_bold2 = 'bold';
                                } else {
                                    $report_bold2 = 'normal';
                                }									

                                if($valRight['report_type2'] == 1){
                                    $tblitem_right1 = "
                                        <tr>
                                            <td colspan=\"2\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                        </tr>";
                                } else {
                                    $tblitem_right1 = "";
                                }



                                if($valRight['report_type2'] == 2){
                                    $tblitem_right2 = "
                                        <tr>
                                            <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                            <td style=\"width: 30%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
                                        </tr>";
                                } else {
                                    $tblitem_right2 = "";
                                }									

                                if($valRight['report_type2']	== 3){
                                    $last_balance2 	= $this->getAmountAccount($valRight['account_id2']);

                                    $tblitem_right3 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
                                        </tr>";

                                    $account_amount2_bottom[$valRight['report_no']] = $last_balance2;
                                } else {
                                    $tblitem_right3 = "";
                                }

                                if($valRight['report_type2']	== 10){
                                    $last_balance210 	= $this->getAmountAccount($valRight['account_id2']);		


                                    $account_amount210_top[$valRight['report_no']] = $last_balance210;

                                } else {
                                }
                                

                                if($valRight['report_type2'] == 11){
                                    if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                                        $report_formula2 	= explode('#', $valRight['report_formula2']);
                                        $report_operator2 	= explode('#', $valRight['report_operator2']);

                                        $total_account_amount210	= 0;
                                        for($i = 0; $i < count($report_formula2); $i++){
                                            if($report_operator2[$i] == '-'){
                                                if($total_account_amount210 == 0 ){
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount210 = $total_account_amount210 - $account_amount210_top[$report_formula2[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount210 == 0){
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount210;

                                        $tblitem_right10 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount210, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_right10 = "";
                                    }
                                } else {
                                    $tblitem_right10 = "";
                                }

                                if($valRight['report_type2'] == 8){
                                    $sahu_tahun_lalu = $this->AcctProfitDayReportNew1_model->getSHUTahunLalu($month, $year);

                                    if(empty($sahu_tahun_lalu)){
                                        $sahu_tahun_lalu = 0;
                                    }


                                    
                                    $tblitem_right8 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($sahu_tahun_lalu, 2)."</td>
                                        </tr>
                                        ";

                                    $account_amount2_bottom[$valRight['report_no']] = $sahu_tahun_lalu;
                                } else {
                                    $tblitem_right8 = "";
                                }

                                if($valRight['report_type2'] == 7){
                                    $profit_loss = $this->AcctProfitDayReportNew1_model->getProfitLossAmount($month, $year);

                                    if(empty($profit_loss)){
                                        $profit_loss = 0;
                                    }

                                    
                                    $tblitem_right7 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($profit_loss, 2)."</td>
                                        </tr>
                                        ";

                                    $account_amount2_bottom[$valRight['report_no']] = $profit_loss;
                                } else {
                                    $tblitem_right7 = "";
                                }
                                

                                if($valRight['report_type2'] == 5){
                                    if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                                        $report_formula2 	= explode('#', $valRight['report_formula2']);
                                        $report_operator2 	= explode('#', $valRight['report_operator2']);

                                        $total_account_amount2	= 0;
                                        for($i = 0; $i < count($report_formula2); $i++){
                                            if($report_operator2[$i] == '-'){
                                                if($total_account_amount2 == 0 ){
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
                                                }
                                            } else if($report_operator2[$i] == '+'){
                                                if($total_account_amount2 == 0){
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount2;

                                        $tblitem_right5 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2+$total_account_amount210, 2)."</div></td>
                                            </tr>";
                                        // $tblitem_right5 = "
                                        //     <tr>
                                        //         <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                        //         <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2, 2)."</div></td>
                                        //     </tr>";
                                    } else {
                                        $tblitem_right5 = "";
                                    }
                                } else {
                                    $tblitem_right5 = "";
                                }


                                

                                $tblitem_right .= $tblitem_right1.$tblitem_right2.$tblitem_right3.$tblitem_right10.$tblitem_right8.$tblitem_right7.$tblitem_right5;

                                
                            }

                $tblfooter_right = "
                        </table>
                    </td>";

        $tblFooter = "
            </tr>
            <tr>
                <td style=\"width: 50%\">
                    <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
                        <tr>
                            <td style=\"width: 60%\"><div style=\"font-weight:".$report_bold1.";font-size:12px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                            <td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1, 2)."</div></td>
                        </tr>
                    </table>
                </td>
                <td style=\"width: 50%\">
                    <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
                        <tr>
                            <td style=\"width: 60%\"><div style=\"font-weight:".$report_bold2.";font-size:12px\">".$report_tab2."".$valRight['account_name2']."</div></td>
                            <td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2, 2)."</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>";
            
        $table = $tblHeader.$tblheader_left.$tblitem_left.$tblfooter_left.$tblheader_right.$tblitem_right.$tblfooter_right.$tblFooter;
            /*print_r("table ");
            print_r($table);
            exit;*/

        $pdf::writeHTML($table, true, false, false, false, '');


        $filename = 'Laporan_Neraca.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportProfitDayReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('d');
        } else {
            $end_date = Session::get('end_date');
        }  


        $data = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher_item.account_id',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->whereIn('acct_journal_voucher_item.account_id',['28','52','46'])
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();

        $hasil = 0;
        $hasil2 = 0;
        $hasil3 = 0;

        $data2 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher_item.account_id','acct_journal_voucher_item.account_id_status',
        DB::Raw("SUM(CASE WHEN acct_journal_voucher.transaction_module_id = '2' THEN acct_journal_voucher_item.journal_voucher_amount ELSE -acct_journal_voucher_item.journal_voucher_amount END) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher_item.account_id',36)
        ->whereIn('acct_journal_voucher.transaction_module_id', ['2','7'])
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();



        $data3 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher_item.account_id', 56)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->get();

                $data4 = JournalVoucher::select('acct_journal_voucher.journal_voucher_date','acct_journal_voucher.journal_voucher_description','acct_journal_voucher_item.account_id_status',DB::Raw("SUM(acct_journal_voucher_item.journal_voucher_amount) as journal_voucher_amount"))
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
	    ->where('acct_journal_voucher.backup_status',0)
        ->where('acct_journal_voucher.transaction_module_id', 1)
        ->where('acct_journal_voucher_item.account_id_default_status', 0)
        ->where('acct_journal_voucher_item.account_id_status', 1)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->groupBy('acct_journal_voucher_item.account_id')
        ->groupBy('acct_journal_voucher.journal_voucher_description')
        ->groupBy('acct_journal_voucher.journal_voucher_date')
        ->orderBy('acct_journal_voucher.journal_voucher_date', 'ASC')
        ->orderBy('acct_journal_voucher.journal_voucher_id', 'ASC')
        ->get();

        // dd($data2);
        $spreadsheet = new Spreadsheet();
        if(($data && $data2)){
            $spreadsheet->getProperties()->setCreator("SIS Integrated System")
                                    ->setLastModifiedBy("SIS Integrated System")
                                    ->setTitle("Laporan Neraca")
                                    ->setSubject("")
                                    ->setDescription("Laporan Neraca")
                                    ->setKeywords("Laba Rugi, Laporan, SIS, Integrated")
                                    ->setCategory("Laporan 10 Hari");
                                    
            $sheet = $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(20);

            
            $spreadsheet->getActiveSheet()->mergeCells("B1:O1");
            $spreadsheet->getActiveSheet()->mergeCells("B2:O2");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);


            $spreadsheet->getActiveSheet()->getStyle('B4:O4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B3:O3')->getFont()->setBold(true);	
            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('G3:J3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('L3:O3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);



            $spreadsheet->getActiveSheet()->setCellValue('B1',"Laporan 10 Hari");	
            $spreadsheet->getActiveSheet()->setCellValue('B2', 'Period '.date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));
            
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Tanggal");
            $sheet->setCellValue('D3',"Keterangan");
            $sheet->setCellValue('E3',"Jumlah");

            $a=4;
            $no=0;
            $total1=0;
            foreach($data as $key=>$val){
                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$a.':E'.$a)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('B'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $id = $val['journal_voucher_id'];
                    
                    if($val['journal_voucher_id'] == $id){
                        if($val['account_id'] == 28){
                        $no++;
                        $sheet->setCellValue('B'.$a, $no);
                        $sheet->setCellValue('C'.$a, $val['journal_voucher_date']);
                        $sheet->setCellValue('D'.$a, 'Pendapatan Menu');
                        $sheet->setCellValue('E'.$a, number_format($val['journal_voucher_amount'],2,'.',','));
                        }elseif($val['account_id'] == 52){
                        $no++;
                        $sheet->setCellValue('B'.$a, $no);
                        $sheet->setCellValue('C'.$a, $val['journal_voucher_date']);
                        $sheet->setCellValue('D'.$a, 'Pendapatan Konsinyasi');
                        $sheet->setCellValue('E'.$a, number_format($val['journal_voucher_amount'],2,'.',','));
                        }else{
                        $no++;
                        $sheet->setCellValue('B'.$a, $no);
                        $sheet->setCellValue('C'.$a, $val['journal_voucher_date']);
                        $sheet->setCellValue('D'.$a, 'Pendapatan Lain Lain');
                        $sheet->setCellValue('E'.$a, number_format($val['journal_voucher_amount'],2,'.',','));
                        }
                }else{
                    continue;
                }
                $total1 += $val['journal_voucher_amount'];

                $a++;
            }
        }

        // $a -= count($data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$a.':E'.$a)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->mergeCells('B'.$a.':D'.$a);

        $spreadsheet->getActiveSheet()->getStyle('B'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('E'.$a)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('B'.$a,'Total');
        $sheet->setCellValue('E'.$a, number_format($total1,2,'.',','));


            $sheet->setCellValue('G3',"No");
            $sheet->setCellValue('H3',"Tanggal");
            $sheet->setCellValue('I3',"Keterangan");
            $sheet->setCellValue('J3',"Jumlah");
            $b=4;
            $no=0;
            $total2=0;

            foreach($data3 as $key=>$val){
                if(is_numeric($key)){
                    $spreadsheet->getActiveSheet()->getStyle('G'.$b.':J'.$b)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    
                    $sheet = $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('J'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $id = $val['journal_voucher_id'];
                    
                    if($val['journal_voucher_id'] == $id){

                        $no++;
                        $sheet->setCellValue('G'.$b, $no);
                        $sheet->setCellValue('H'.$b, $val['journal_voucher_date']);
                        $sheet->setCellValue('I'.$b, 'Kas Kecil');
                        $sheet->setCellValue('J'.$b, number_format($val['journal_voucher_amount'],2,'.',','));
                    }
                    
                }else{
                    continue;
                }
                $total2 += $val['journal_voucher_amount'];

                $b++;
        
            }

            $spreadsheet->getActiveSheet()->getStyle('G'.$b.':J'.$b)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('G'.$b.':I'.$b);

            $spreadsheet->getActiveSheet()->getStyle('G'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('J'.$b)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue('G'.$b,'Total');
                    $sheet->setCellValue('J'.$b, number_format($total2,2,'.',','));


            $sheet->setCellValue('L3',"No");
            $sheet->setCellValue('M3',"Tanggal");
            $sheet->setCellValue('N3',"Keterangan");
            $sheet->setCellValue('O3',"Jumlah");
            $j=4;
            $no=0;
            $total3=0;

            foreach($data2 as $key=>$val){
                if(is_numeric($key)){
                    
                    $spreadsheet->getActiveSheet()->getStyle('L'.$j.':O'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
                    $sheet = $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    
                    $id = $val['journal_voucher_id'];
                    
                    if($val['journal_voucher_id'] == $id){
    
                        $no++;
                        $sheet->setCellValue('L'.$j, $no);
                        $sheet->setCellValue('M'.$j, $val['journal_voucher_date']);
                        $sheet->setCellValue('N'.$j, 'Pembelian Bahan Baku');
                        $sheet->setCellValue('O'.$j, number_format($val['journal_voucher_amount'],2,'.',','));
                }else{
                    continue;
                }
                $total3 += $val['journal_voucher_amount'];
                $j++;
                
            }
        } 

            foreach($data4 as $key=>$val){
                if(is_numeric($key)){
                    
                    $spreadsheet->getActiveSheet()->getStyle('L'.$j.':O'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $sheet = $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $id = $val['journal_voucher_id'];
                    
                    if($val['journal_voucher_id'] == $id){

			            $no++;
                        $sheet->setCellValue('L'.$j, $no);
                        $sheet->setCellValue('M'.$j, $val['journal_voucher_date']);
                        $sheet->setCellValue('N'.$j, $val['journal_voucher_description']);
                        $sheet->setCellValue('O'.$j, number_format($val['journal_voucher_amount'],2,'.',','));
                }else{
                    continue;
                }
                $total3 += $val['journal_voucher_amount'];
                $j++;
                
            }
        }   
            $s=3;
            $setor=0;

            $setor= ($total1 - $total3) + $total2;
            $s++;

                $spreadsheet->getActiveSheet()->getStyle('L'.$j.':O'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->mergeCells('L'.$j.':N'.$j);

                $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('L'.$j,'Total');
                $sheet->setCellValue('O'.$j, number_format($total3,2,'.',','));

                $j += 2;
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('B'.$j,'Total Disetor Ke IBU TOMO');
                $sheet->setCellValue('E'.$j, number_format($setor,2,'.',','));

                $j += 3;
                $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('B' . $j, 'Yang Melaporkan');

                $spreadsheet->getActiveSheet()->mergeCells('L'.$j.':N'.$j);
                $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('L' . $j, 'Mengetahui,');

                $j += 3;
                $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('B' . $j, 'FARIDA ISMIJATI');

                $spreadsheet->getActiveSheet()->mergeCells('L'.$j.':N'.$j);
                $spreadsheet->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('L' . $j, 'Ibu Puspa Dewi Utomo');
                
            $filename='Laporan_10_Hari_'.$start_date.'_s.d._'.$end_date.'.xls';
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
