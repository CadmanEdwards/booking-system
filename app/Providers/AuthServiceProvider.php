<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $user = \Auth::user();

        
        // Auth gates for: User management
        Gate::define('user_management_access', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Roles
        Gate::define('role_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        Gate::define('taxrate_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
      
      //Google Calender Rights
      
       Gate::define('google_calendar_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('google_calendar_sync_create', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Users
        Gate::define('user_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });
		
        // Auth gates for: Services
        Gate::define('service_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('service_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('service_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('service_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('service_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });		

          // Auth gates for: Room
        Gate::define('room_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('room_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('room_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('room_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('room_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });     
        

        
    // Auth gates for: Users
        Gate::define('calandar_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        
         Gate::define('availability_access', function ($user) {
            return in_array($user->role_id, [2,3]);
        });

         // Auth gates for: Location
        Gate::define('location_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('location_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('location_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('location_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('location_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        Gate::define('emailtemplate_access', function ($user) {
            return in_array($user->role_id, [1]);
        });     
        Gate::define('emailtemplate_create', function ($user) {
            return in_array($user->role_id, [1]);
        });     
           Gate::define('emailtemplate_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('emailtemplate_delete', function ($user) {
            return in_array($user->role_id, [1]);
        }); 

        // Auth gates for: Doctor
        Gate::define('doctor_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('doctor_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
         Gate::define('doctor_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('doctor_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('doctor_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });


        // Auth gates for: Clients
        Gate::define('client_access', function ($user) {
            return in_array($user->role_id, [1,  3]);
        });
        Gate::define('client_create', function ($user) {
            return in_array($user->role_id, [1,  3]);
        });
        Gate::define('client_json_create', function ($user) {
            return in_array($user->role_id, [1,  3]);
        });
        
        Gate::define('client_without_moneybird', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        
        Gate::define('client_edit', function ($user) {
            return in_array($user->role_id, [1,  2,  3]);
        });
        Gate::define('client_view', function ($user) {
            return in_array($user->role_id, [1,  2, 3]);
        });
        Gate::define('client_delete', function ($user) {
            return in_array($user->role_id, [1,    3]);
        });

        // Auth gates for: Employees
        Gate::define('employee_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        Gate::define('employee_small_info', function ($user) {
            return in_array($user->role_id, [3]);
        });

        // Auth gates for: Employee Services
        Gate::define('employee_service_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_service', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_service_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_service_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_service_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('employee_service_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Working hours
        Gate::define('working_hour_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('working_hour_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('working_hour_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('working_hour_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('working_hour_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Invoice
        Gate::define('invoice_access', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('invoice_create', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('invoice_edit', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('invoice_view', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('invoice_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });
        

        // Auth gates for: Appointments
        Gate::define('appointment_access', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('appointment_create', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('appointment_edit', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('appointment_view', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('appointment_delete', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        
        // Auth gates for: Appointments For Opertor
        Gate::define('oappointment_access', function ($user) {
            return in_array($user->role_id, [2]);
        });
        Gate::define('oappointment_create', function ($user) {
            return in_array($user->role_id, [1,2,3]);
        });
        Gate::define('oappointment_edit', function ($user) {
            return in_array($user->role_id, [2]);
        });
        Gate::define('oappointment_view', function ($user) {
            return in_array($user->role_id, [2]);
        });
        Gate::define('oappointment_delete', function ($user) {
            return in_array($user->role_id, [2]);
        });



          // Auth gates for: Leave
        Gate::define('leave_access', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('leave_create', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('leave_edit', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('leave_view', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('leave_delete', function ($user) {
            return in_array($user->role_id, [1,3]);
        });

         // Auth gates for: Leave
        Gate::define('employee_custom_timing_access', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('employee_custom_timing_create', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('employee_custom_timing_edit', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('employee_custom_timing_view', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
        Gate::define('employee_custom_timing_delete', function ($user) {
            return in_array($user->role_id, [1,3]);
        });
      //working hours created and update only for therapist
        Gate::define('thrapist_working_hour_create', function ($user) {
            return in_array($user->role_id, [3]);
        });
        Gate::define('thrapist_working_hour_save', function ($user) {
            return in_array($user->role_id, [3]);
        });


        // Auth gates for: company access
        Gate::define('company_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('company_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('company_edit', function ($user) {
            return in_array($user->role_id, [1,2]);
        });
        Gate::define('company_view', function ($user) {
            return in_array($user->role_id, [1,2,3,4]);
        });
        Gate::define('company_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });    
        

        // Auth gates for: Custom Pages
        Gate::define('page_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('page_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('page_edit', function ($user) {
            return in_array($user->role_id, [1,2]);
        });
        Gate::define('page_view', function ($user) {
            return in_array($user->role_id, [1,2,3,4]);
        });
        Gate::define('page_delete', function ($user) {
            return in_array($user->role_id, []);
        });    
        Gate::define('page_present_view', function ($user) {
            return in_array($user->role_id, [2,3,4,5]);
        });
    }
}
