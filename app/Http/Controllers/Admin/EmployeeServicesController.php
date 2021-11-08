<?php

namespace App\Http\Controllers\Admin;

use App\EmployeeService;
use App\Employee;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeServiceRequest;
use App\Http\Requests\Admin\UpdateEmployeeServiceRequest;
use DB;
use App\User;


class EmployeeServicesController extends Controller
{
     
    /**
     * Show the form for creating new Employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if (! Gate::allows('employee_service_create')) {
            return abort(401);
        }
       $check_service_Id= \App\EmployeeService::select('service_id')->where('employee_id','=',$id)->get();
       $notin='';
       if(isset($check_service_Id))
         {
              $ids=array();
             foreach($check_service_Id as $check_service_array)
                  {
                    $ids[] = $check_service_array['service_id'];
                  }

             $notin = implode(',', $ids);

         }
        
       
		$relations = [
            'services' => \App\Service::whereNotIn('id', [$notin])->get()->pluck('name', 'id')->prepend('Please select', ''),
            'employee' => \App\Employee::where('id','=',$id)->first(),
        ];  
        return view('admin.employee_services.create', $relations);
    }

    /**
     * Store a newly created Employee Service in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeServiceRequest $request)
    {
        if (! Gate::allows('employee_service_create')) {
            return abort(401);
        }
        
        $empservice = EmployeeService::create($request->all());
        return redirect()->route('admin.employees.services',[$request->employee_id]);
    } 

    /**
     * Show the form for editing Employee Service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('employee_service_edit')) {
            return abort(401);
        }
        $employeeservices = EmployeeService::findOrFail($id);
        $relations = [
            'services' => \App\Service::get()->pluck('name', 'id')->prepend('Please select', ''),
            'employee' => \App\Employee::where('id','=',$employeeservices->employee_id)->first(),
        ];  
        return view('admin.employee_services.edit', compact('employeeservices') + $relations);
    }

    /**
     * Update Employee Service in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeServiceRequest $request, $id)
    {
        if (! Gate::allows('employee_service_edit')) {
            return abort(401);
        }
        $employee = EmployeeService::findOrFail($id); 

        $employee->update($request->all()); 
        return redirect()->route('admin.employees.services',[$request->employee_id]);
    }
 
    /**
     * Remove Employee Service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('employee_service_delete')) {
            return abort(401);
        }
        $employee = EmployeeService::findOrFail($id);
        $eid = $employee->employee_id;
        $employee->delete();

        return redirect()->route('admin.employees.services',[$eid]);
    }

    /**
     * Delete all selected Employee Service at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('employee_service_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Employee::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
	 

}
