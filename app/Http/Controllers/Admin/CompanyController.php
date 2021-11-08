<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Roles;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Gate;
use App\TblCompanies;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('company_access')) { return abort(401); }

        $user = \Auth::user();   
        /*if($user->role_id != 1)
           { return redirect()->route('admin.company.edit',[$user->id]); } */
        return redirect()->route('admin.companies.edit',[$user->id]); 

        $Companies = TblCompanies::all();


       return view('admin.Companies.index', compact('Companies'));

        


        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (! Gate::allows('company_create')) {
            return abort(401);
        }
        

        return view('admin.Companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if (! Gate::allows('company_create')) {
            return abort(401);
        }
        $company = TblCompanies::create($request->all()); 
        return redirect()->route('admin.companies.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('company_edit')) {
            return abort(401);
        }


        $company = TblCompanies::findOrFail($id);
       

        return view('admin.Companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (! Gate::allows('company_edit')) {
            return abort(401);
        }
        $logo1 = $logo2 = "";
         $emplogo=time();
         if(!empty($request->file('logo1_file')))
         {
            $image = $request->file('logo1_file');
            $logo1 = "clogo1_".$emplogo.'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload');
            $image->move($destinationPath, $logo1);
         }
         if(!empty($request->file('logo2_file')))
         {
            $image = $request->file('logo2_file');
            $logo2 = "clogo2_".$emplogo.'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload');
            $image->move($destinationPath, $logo2);
         }
        $companyRs = $request->all();  

        if(!empty($logo1)) { $companyRs['cmp_logo1'] = $logo1; }
        if(!empty($logo2)) { $companyRs['cmp_logo2'] = $logo2; }
        
        $company = TblCompanies::findOrFail($id);
        $company->update($companyRs); 

        return redirect()->route('admin.companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('company_delete')) {
            return abort(401);
        }
        $company = TblCompanies::findOrFail($id)->delete();
        return redirect()->route('admin.companies.index');
    }
}
