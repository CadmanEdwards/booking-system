<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Moneybird;  
use \App;
use App\TaxRate;
use App\Client;
use Illuminate\Support\Facades\Gate;
class TaxRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

          if (! Gate::allows('taxrate_access')) {
            return abort(401);
        }
   
         $appointments = TaxRate::get();

         
        return view('admin.taxrates.index', compact('appointments') );
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getalltaxratesave()
    {
        // all tax class grab from Moneybrid and inserted into our database so when we create therapy we can add that tax class into and if appointment is booked we can sent that tax class id with that appointment and it will automatically created VAT class for that appointment on Moneybird
        $taxRates = Moneybird::taxRate()->get();
       
        foreach ($taxRates as $taxRate) {
             $taxrate = new TaxRate;
             $taxrate->name = $taxRate->name;
             $taxrate->percentage = $taxRate->percentage;
             $taxrate->tax_rate_type = $taxRate->tax_rate_type;
             $taxrate->show_tax = $taxRate->show_tax;
             $taxrate->active = $taxRate->active;
             $taxrate->moneybird_tax_id = $taxRate->id;

             $taxrate->save();


        }

        
    }
   
}
