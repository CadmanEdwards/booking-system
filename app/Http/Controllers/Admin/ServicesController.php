<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Service;
use App\ServiceExtraCost;
use App\EmployeeService;
use App\Appointment;
use App\TaxRate;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('service_access')) {
            return abort(401);
        }

        $services = Service::all();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('service_create')) {
            return abort(401);
        }
        $relations = [
            'vatstaus' => array('taxable'=>'Taxable','shipping'=>'Send Only','none'=>'No'),
            'blockdurationunit' => array('minute'=>'Minute (s)','day'=>'Day (s)','hour'=>'Hour (s)','month'=>'Month (s)'),
            'bookingservicetype' => array('time'=>'Time Series(entire week)','week_days'=>'Weekend','eve_time' => 'Evening Time'),
            'basiccostdurationunit' => array('plus'=>'+','times'=>'*','minus'=>'-','divide'=>'%'),
            'blockcostdurationunit' =>  array('plus'=>'+','times'=>'*','minus'=>'-','divide'=>'%'),
            'taxrate' => \App\TaxRate::where('tax_rate_type','=','sales_invoice')->where('active','=',
                '1')->get()
        ];
        return view('admin.services.create',$relations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceRequest $request)
    {
        if (! Gate::allows('service_create')) {
            return abort(401);
        }


       
        $service = Service::create($request->all());
        $extra_cost = [];


        // Need to add one to our number of slots because
        // the $i count starts at one instead of zero.
       if($request->booking_series_type != null)
       { 
         foreach($request->booking_series_type as $key => $values) {
            
            $booking_basic_price ='';$booking_block_price =''; 
            $booking_basic_cost_duration_type_unit='';
            $booking_block_cost_duration_type_unit='';
            $booking_pricing_time_from='';
            $booking_pricing_time_to='';

            if(isset($request->booking_basic_price[$key]))
               { $booking_basic_price =$request->booking_basic_price[$key];}   
            
            if(isset($request->booking_block_price[$key]))
               { $booking_block_price =$request->booking_block_price[$key];}   
           
            if(isset($request->booking_basic_cost_duration_type_unit[$key]))
               { $booking_basic_cost_duration_type_unit =$request->booking_basic_cost_duration_type_unit[$key];}   
            
            if(isset($request->booking_block_cost_duration_type_unit[$key]))
               { $booking_block_cost_duration_type_unit =$request->booking_block_cost_duration_type_unit[$key];}   

             if(isset($request->booking_pricing_time_from[$key]))
               { $booking_pricing_time_from =$request->booking_pricing_time_from[$key];}

            if(isset($request->booking_pricing_time_to[$key]))
               { $booking_pricing_time_to =$request->booking_pricing_time_to[$key];}


           $extra_cost[] =  new ServiceExtraCost([
             'service_id' => $service->getKey(),
             'booking_basic_pricing' => $booking_basic_price,
             'booking_block_pricing' => $booking_block_price,
             'booking_series_type' => $values,
             'booking_basic_cost_duration_type_unit'=>$booking_basic_cost_duration_type_unit,
             'booking_block_cost_duration_type_unit'=>$booking_block_cost_duration_type_unit,
             'booking_pricing_time_from' => $booking_pricing_time_from,
             'booking_pricing_time_to' => $booking_pricing_time_to,
            ]);
        }

       } 
       
     
        $service->extra_cost()->saveMany($extra_cost);
        return redirect()->route('admin.services.index');
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
        if (! Gate::allows('service_edit')) {
            return abort(401);
        }
        $service = Service::findOrFail($id); 
         $relations = [
            'vatstaus' => array('taxable'=>'Taxable','shipping'=>'Send Only','none'=>'No'),
            'blockdurationunit' => array('minute'=>'Minute (s)','day'=>'Day (s)','hour'=>'Hour (s)','month'=>'Month (s)'),
            'bookingservicetype' => array('time'=>'Time Series(entire week)','week_days'=>'Weekend','eve_time' => 'Evening Time'),
            'basiccostdurationunit' => array('plus'=>'+','times'=>'*','minus'=>'-','divide'=>'%'),
            'blockcostdurationunit' =>  array('plus'=>'+','times'=>'*','minus'=>'-','divide'=>'%'),
             'taxrate' => \App\TaxRate::where('tax_rate_type','=','sales_invoice')->where('active','=',
                '1')->get()
        ];
        return view('admin.services.edit', compact('service')+ $relations);
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
         if (! Gate::allows('service_edit')) {
            return abort(401);
        }
        $service = Service::find($id);
        $service->update($request->all());

        $extra_cost = [];
        $service->extra_cost()->delete();
        if(isset($request->booking_series_type))
         {
              foreach($request->booking_series_type as $key => $values) {
            
            $booking_basic_price ='';$booking_block_price =''; 
            $booking_basic_cost_duration_type_unit='';
            $booking_block_cost_duration_type_unit='';
            $booking_pricing_time_from='';
            $booking_pricing_time_to='';

            if(isset($request->booking_basic_price[$key]))
               { $booking_basic_price =$request->booking_basic_price[$key];}   
            
            if(isset($request->booking_block_price[$key]))
               { $booking_block_price =$request->booking_block_price[$key];}   
           
            if(isset($request->booking_basic_cost_duration_type_unit[$key]))
               { $booking_basic_cost_duration_type_unit =$request->booking_basic_cost_duration_type_unit[$key];}   
            
            if(isset($request->booking_block_cost_duration_type_unit[$key]))
               { $booking_block_cost_duration_type_unit =$request->booking_block_cost_duration_type_unit[$key];}   

             if(isset($request->booking_pricing_time_from[$key]))
               { $booking_pricing_time_from =$request->booking_pricing_time_from[$key];}

            if(isset($request->booking_pricing_time_to[$key]))
               { $booking_pricing_time_to =$request->booking_pricing_time_to[$key];}


           $extra_cost[] =  new ServiceExtraCost([
             'service_id' => $service->getKey(),
             'booking_basic_pricing' => $booking_basic_price,
             'booking_block_pricing' => $booking_block_price,
             'booking_series_type' => $values,
             'booking_basic_cost_duration_type_unit'=>$booking_basic_cost_duration_type_unit,
             'booking_block_cost_duration_type_unit'=>$booking_block_cost_duration_type_unit,
             'booking_pricing_time_from' => $booking_pricing_time_from,
             'booking_pricing_time_to' => $booking_pricing_time_to,
            ]);
        }
         }
     
        $service->extra_cost()->saveMany($extra_cost);
        return redirect()->route('admin.services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('service_delete')) {
            return abort(401);
        }
        $service = Service::find($id);
        $Employeeentries = EmployeeService::where('service_id', $id)->get();
        $appointments = Appointment::where('service_id', $id)->get();
        
        if(count($Employeeentries) > 0 || count($appointments) > 0)
        {
            return redirect()->route('admin.services.index')->withInput()->with('error', 'Thaerpy can not be deleted becuase its alreday assign to Therapist or this therapy alreday in appintments as well');
        }
        else
         {
           $service->extra_cost()->delete();
           $service->delete();
          return redirect()->route('admin.services.index')->with('success', 'Therapy is deleted Successfully');
         }
    }
	
	public function massDestroy(Request $request)
    {
        if (! Gate::allows('service_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Service::whereIn('id', $request->input('ids'))->get();
            


            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
}
