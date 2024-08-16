<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\CoreSection;
use App\Models\PreferenceCompany;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Components\Widget\Alert;

class SystemPPNController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $preferencecompany = PreferenceCompany::select()
        ->where('company_id', Auth::user()->company_id)
        ->first();

        return view('content/SystemPPN/FormEditSystemPPN',compact('preferencecompany'));
    }

    public function processEditSystemPPN(Request $request)
    {
        $fields = $request->validate([
            'ppn_percentage'    => 'required',
        ]);

        $preferencecompany                  = PreferenceCompany::findOrFail(Auth::user()->company_id);
        $preferencecompany->ppn_percentage  = $fields['ppn_percentage'];

        if($preferencecompany->save()){
            $msg = 'Edit Persentase PPN Berhasil';
            return redirect('/system-ppn')->with('msg',$msg);
        }else{
            $msg = 'Edit Persentase PPN Gagal';
            return redirect('/system-ppn')->with('msg',$msg);
        }
    }  
}
