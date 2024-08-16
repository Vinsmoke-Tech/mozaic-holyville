<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JournalCategories;
use App\Models\JournalCategoriesFormula;
use App\Models\AcctAccount;
use App\Models\JournalCategoriesFormulaAccount;
use App\Models\AcctJournalVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class JournalCategoriesFormulaController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index($journal_categories_id)
    {
        $data = JournalCategoriesFormula::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('journal_categories_id', $journal_categories_id )
        ->get();

        $datachild = JournalCategoriesFormula::join('journal_categories_formula_account','journal_categories_formula_account.categories_formula_id','=','journal_categories_formula.categories_formula_id')
        ->join('acct_account', 'acct_account.account_id','=','journal_categories_formula_account.account_id')
        ->where('journal_categories_formula.data_state', 0)
        ->where('journal_categories_formula.company_id', Auth::user()->company_id)
        ->where('journal_categories_formula.journal_categories_id', $journal_categories_id )
        ->get();

        return view('content.JournalCategoriesFormula.ListJournalCategoriesFormula', compact('data','journal_categories_id','datachild'));

    }

    public function addJournalCategoriesFormula(Request $request)
    {
        $journal_categories_id = $request->journal_categories_id;
        $journal = Session::get('journal');
        $arraydata = Session::get('arraydatases');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        $account = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        
        return view('content.JournalCategoriesFormula.FormAddJournalCategoriesFormula',compact('status','account','journal','arraydata','journal_categories_id'));
    }



    public function processAddJournalCategoriesFormula(Request $request)
    {
        $fields = $request->validate([
            'journal_categories_id'           => 'required',
            'journal_categories_item_formula_name'           => 'required',
            'journal_categories_item_formula_description'    => 'required'

        ]);

        $datases = array(
            'journal_categories_item_formula_name'          => $fields['journal_categories_item_formula_name'],
            'journal_categories_item_formula_description'   => $fields['journal_categories_item_formula_description'],
            'journal_categories_id'                         => $fields['journal_categories_id'],
            'company_id'                                    => Auth::user()->company_id,
            'updated_id'                                    => Auth::id(),
            'created_id'                                    => Auth::id(),
        );
        // dd($data);exit;

        if(JournalCategoriesFormula::create($datases)){

            $categories_formula_id = JournalCategoriesFormula::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            // $account_default_status = $this->getAccountDefaultStatus($data['account_id']);
            $arraydata = Session::get('arraydatases');
            foreach($arraydata as $val){
                if($val['account_status'] == 0){
                    $data = array(
                        'categories_formula_id'   => $categories_formula_id['categories_formula_id'],
                        'account_id'                    => $val['account_id'],
                        'account_id_status'             => $val['account_status'],
                        'company_id'                    => Auth::user()->company_id,
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalCategoriesFormulaAccount::create($data);
                } else {
                    $data = array(
                        'categories_formula_id'            => $categories_formula_id['categories_formula_id'],
                        'account_id'                    => $val['account_id'],
                        'account_id_status'             => $val['account_status'],
                        'company_id'                    => Auth::user()->company_id,
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalCategoriesFormulaAccount::create($data);
                }
            }

            $msg = 'Tambah Kategori Berhasil';
            return redirect('/journal-categories-formula/'.$request->journal_categories_id)->with('msg',$msg);
        } else {
            $msg = 'Tambah Kategori Gagal';
            return redirect('/journal-categories-formula/add')->with('msg',$msg);
        }
    }

    public function editJournalCategoriesFormula($journal_categories_id)
    {
        $data = JournalCategories::where('journal_categories_id',$journal_categories_id)->first();
        return view('content.JournalCategories.FormEditJournalCategoriesFormula', compact('data'));
    }

    public function processEditJournalCategoriFormula(Request $request)
    {
        $fields = $request->validate([
            'journal_categories_id'       => '',
            'journal_categories_name'     => 'required',
        ]);

        $table                          = JournalCategories::findOrFail($fields['journal_categories_id']);
        $table->journal_categories_name      = $fields['journal_categories_name'];
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Ubah Kategori Barang Berhasil";
            return redirect('/journal-categories-formula/'.$request->journal_categories_id)->with('msg', $msg);
        } else {
            $msg = "Ubah Kategori Barang Gagal";
            return redirect('/journal-categories-formula')->with('msg', $msg);
        }
    }

    public function deleteJournalCategoriFormula($journal_categories_id)
    {
        $table              = JournalCategories::findOrFail($journal_categories_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Hapus Jurnal Khusus Berhasil";
            return redirect('/journal-categories-formula')->with('msg', $msg);
        } else {
            $msg = "Hapus Jurnal Khusus Gagal";
            return redirect('/journal-categories-formula')->with('msg', $msg);
        }
    }

    public function resetAddJournalCategoriFormula(Request $request)
    {   
  
        Session::forget('journal');
        Session::forget('arraydatases');

        return redirect( '/add');

    }

    public function addElementsJournalCategoriFormula(Request $request)
    {
        $journal = Session::get('journal');
        if(!$journal || $journal == ''){
            $journal['journal_categories_item_formula_name']        = '';
            $journal['journal_categories_item_formula_description'] = '';
        }

        $journal[$request->name] = $request->value;
        Session::put('journal',$journal);
    }

    public function addArrayJournalCategoriFormula(Request $request)
    {
        $request->validate([
            'journal_categories_id' => '',
            'account_id'                => 'required',
            'account_status'            => 'required',
            
        ]);

        $arraydatases = array(
            'journal_categories_id'     => $request->journal_categories_id,
            'account_id'                => $request->account_id,
            'account_status'            => $request->account_status,
            
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

        return redirect( 'journal-categories-formula/'.$request->journal_categories_id.'/add');
    }

    public function getAccountCode($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_code'];
    }

    public function getAccountName($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_name'];
    }

    public function getStatus($account_status)
    {
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        return $status[$account_status];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();
        // dd($data);exit;
        return $data['account_default_status'];
    }


}