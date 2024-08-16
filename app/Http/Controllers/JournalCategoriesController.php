<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JournalCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class JournalCategoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        $data = JournalCategories::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        return view('content.JournalCategories.ListJournalCategories', compact('data'));

    }

    public function addJournalCategories()
    {
        $datacategory = Session::get('datacategory');
        return view('content.JournalCategories.FormAddJournalCategories',compact('datacategory'));
    }



    public function processAddJournalCategories(Request $request)
    {
        $fields = $request->validate([
            'journal_categories_name'     => 'required'
        ]);

        $data = JournalCategories::create([
            'journal_categories_name'   => $fields['journal_categories_name'],
            'company_id'                => Auth::user()->company_id,
            'updated_id'                => Auth::id(),
            'created_id'                => Auth::id(),
        ]);

        if($data->save()){
            $msg = 'Tambah Kategori Berhasil';
            return redirect('/journal-categories')->with('msg',$msg);
        } else {
            $msg = 'Tambah Kategori Gagal';
            return redirect('/journal-categories/add')->with('msg',$msg);
        }
    }

    public function editJournalCategories($journal_categories_id)
    {
        $data = JournalCategories::where('journal_categories_id',$journal_categories_id)->first();
        return view('content.JournalCategories.FormEditJournalCategories', compact('data'));
    }

    public function processEditJournalCategori(Request $request)
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
            return redirect('/journal-categories')->with('msg', $msg);
        } else {
            $msg = "Ubah Kategori Barang Gagal";
            return redirect('/journal-categories')->with('msg', $msg);
        }
    }

    public function deleteJournalCategori($journal_categories_id)
    {
        $table              = JournalCategories::findOrFail($journal_categories_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Hapus Jurnal Khusus Berhasil";
            return redirect('/journal-categories')->with('msg', $msg);
        } else {
            $msg = "Hapus Jurnal Khusus Gagal";
            return redirect('/journal-categories')->with('msg', $msg);
        }
    }

}