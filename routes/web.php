<?php
Route::get('/', function () { return redirect('/admin/home'); });
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');

    Artisan::call('config:clear');

});

Route::get('/reminder', function() {
    Artisan::call('reminder:email');
});

Route::get('/imap_email', function() {
    Artisan::call('fetch:imap_email');
});

Route::get('/reminder-invoice', function() {
    Artisan::call('reminder:invoice');
});

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');
Route::get('adminreminderemail', 'reminderController@index');
Route::get('appointmentverify/{Vtoken}', 'reminderController@token');
Route::get('clientverify/{Vtoken}', 'reminderController@clienttoken');

Route::get('invoicereminder', 'reminderController@invoicereminder');
// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX


Route::get('appointmentinv/{id}','Admin\AppointmentsController@invdownload')->name('appointmentinv');

Route::get('mollie-payment/{id}','Admin\AppointmentsController@preparePayment')->name('mollie.payment');
Route::get('/payment-success/{id}','Admin\AppointmentsController@paymentSuccess')->name('payment.success');
/*Route::post('webhook-payment','MollieController@webhookPayment')->name('webhook.payment');*/



Route::name('webhooks.mollie')->post('webhooks/mollie', 'MollieWebhookController@handle');
Route::name('cpayment.mollie')->get('mollie-cpayment/{id}', 'MollieWebhookController@cpayment');
Route::name('mollie.show')->get('mollie-show/{id}', 'MollieWebhookController@show');
Route::name('mollie.responce')->get('mollie-responce/{id}', 'MollieWebhookController@responce');


//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');
Route::get('createcaptcha', 'CaptchaController@create');
Route::post('captcha', 'CaptchaController@captchaValidate');
Route::get('refreshcaptcha', 'CaptchaController@refreshCaptcha');

Route::get('clear_cache', function () {

    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('config:cache');

    dd("Cache is cleared");

});


Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');
    Route::get('/incoming-invoices', 'Admin\AppointmentsController@upcoming_bookings')->name('upcoming.booking');
    Route::get('/fetch_emails', 'Admin\AppointmentsController@fetch_emails')->name('fetch_emails');
    Route::get('/mark_as_seen_email/{id}', 'Admin\AppointmentsController@mark_as_seen_email')->name('mark_as_seen_email');
    Route::get('/getDownload/{path}', 'Admin\AppointmentsController@getDownload')->name('getDownload');
    Route::get('/download-vcard', 'Admin\AppointmentsController@raf_create_vcard');


    Route::get('/new_create-incoming-invoice', 'Admin\AppointmentsController@new_create_upcoming_booking');

    Route::get('/create-incoming-invoice', 'Admin\AppointmentsController@create_upcoming_booking');
    Route::get('/edit-incoming-invoice/{id}', 'Admin\AppointmentsController@edit_upcoming_booking')->name('edit.upcoming_booking');
    Route::post('/post-create-incoming-invoice', 'Admin\AppointmentsController@post_create_upcoming_booking')->name('post.create.upcoming.booking');
    Route::post('/post-edit-incoming-invoice', 'Admin\AppointmentsController@post_edit_upcoming_booking')->name('post.edit.upcoming.booking');
    Route::get('/delete-incoming-invoice/{id}', 'Admin\AppointmentsController@delete_upcoming_booking')->name('delete.upcoming.booking');

    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
    Route::resource('clients', 'Admin\ClientsController');
    Route::get('clients/view/excel', ['uses' => 'Admin\ClientsController@viewExcel', 'as' => 'clients.view.excel']);
    Route::get('clients/download/excel', ['uses' => 'Admin\ClientsController@downloadExcel', 'as' => 'clients.download.excel']);
    Route::post('clients/post/excel', ['uses' => 'Admin\ClientsController@postExcel', 'as' => 'clients.post.excel']);

    Route::get('clients/remove/notes/{id}', ['uses' => 'Admin\ClientsController@removeNotes', 'as' => 'clients.remove.notes']);
    Route::resource('doctors', 'Admin\DoctorsController');
    Route::get('doctor_destroy/{id}', ['uses' => 'Admin\DoctorsController@destroy', 'as' => 'doctor_destroy']);
    
    Route::get('doctor_copy/{id}', ['uses' => 'Admin\DoctorsController@copy', 'as' => 'doctor_copy']);
   
    Route::resource('calandar', 'Admin\CalandarController');
    Route::get('client_destroy/{id}', ['uses' => 'Admin\ClientsController@destroy', 'as' => 'client_destroy']);
    Route::get('client_vcard/{first_name}/{last_name}/{phone}/{email}', ['uses' => 'Admin\ClientsController@make_vcard', 'as' => 'client_vcard']);

    Route::get('client_copy/{id}', ['uses' => 'Admin\ClientsController@copy', 'as' => 'client_copy']);

    Route::post('clients_mass_destroy', ['uses' => 'Admin\ClientsController@massDestroy', 'as' => 'clients.mass_destroy']);
    Route::post('clients_mass_email_send', ['uses' => 'Admin\ClientsController@massSendEmail', 'as' => 'clients_mass_email_send']);
    Route::post('client_mass_email_confirm_send', ['uses' => 'Admin\ClientsController@massSendEmailConfirmed', 'as' => 'client_mass_email_confirm_send']);
     
      Route::get('get-client-location', 'Admin\ClientsController@GetLocation');
      Route::get('get-client-datatable', 'Admin\ClientsController@datatables');
      Route::get('get-doctor-datatable', 'Admin\DoctorsController@datatables');
    Route::get('settings', ['uses' => 'Admin\UsersController@getSettings', 'as' => 'settings']);
    Route::post('postSettings', ['uses' => 'Admin\UsersController@postSettings', 'as' => 'post.settings']);

      Route::get('appointment-invoices', 'Admin\AppointmentsController@invoices');
      
      

        
      
      Route::get('get-appointment-invoicedatatable','Admin\AppointmentsController@invoicedatatable');
      Route::get('get-appointment-datatable', 'Admin\AppointmentsController@datatables');
      Route::get('get-appointment-exportexcel', 'Admin\AppointmentsController@exportexcel');

      Route::get('get-rooms-location', 'Admin\EmployeesController@GetRoomsLocation');
      Route::post('save-therapists', 'Admin\EmployeesController@store');
      Route::get('get-employees-time-slot', 'Admin\EmployeesController@GetEmployeeTimeSlot');
      Route::get('get-employees-room', 'Admin\EmployeesController@GetEmployeeRoom');
      Route::get('get-appointment-price', 'Admin\EmployeesController@GetServicePrice');
      Route::get('get-employees-edit-time-slot', 'Admin\EmployeesController@GetEmployeeTimeSlotEdit');
    //Route::get('get-client-location', 'Admin\ClientsController@GetLocation');
   //Route::get('get-small-info', );
   
   Route::get('employees/pview/{id}', ['uses' => 'Admin\EmployeesController@pview', 'as' => 'employees.invoice.pview']);

   Route::get('smallinfoedit/{id}', ['uses' => 'Admin\EmployeesController@SmallInfoEdit', 'as' => 'employees.smallinfoedit']); 
   Route::put('smallinfoupdate/{id}', ['uses' => 'Admin\EmployeesController@SmallInfoUpdate', 'as' => 'employees.smallinfoupdate']);  

  //Route::get('pageedit/{id}', ['uses' => 'Admin\PagesController@edit', 'as' => 'page.edit']);
	Route::get('get-employees', 'Admin\EmployeesController@GetEmployees');
  Route::get('update-appointment-status', 'Admin\AppointmentsController@UpdateAppointmentStatus');
    Route::get('get-employees-resource', 'HomeController@EmplyoeeResourceJson'); 
    Route::get('get-time-employees-resource', 'HomeController@EmplyoeeTimeResourceJson'); 
    Route::get('get-employees-appointments', 'HomeController@Emplyoeeappointmentjson'); 
    Route::get('get-all-appointments', 'Admin\AppointmentsController@appointmentjson'); 

    Route::resource('employees', 'Admin\EmployeesController');
    Route::get('disable-therapist/{id}',['uses' => 'Admin\EmployeesController@disableTherapist' , 'as' => 'therapist.disable']);
    Route::get('enable-therapist/{id}',['uses' => 'Admin\EmployeesController@enableTherapist' , 'as' => 'therapist.enable']);
    Route::resource('pages', 'Admin\PagesController');

    Route::resource('companies', 'Admin\CompanyController');

    
    
    Route::get('employees_services/{employee}', ['uses' => 'Admin\EmployeesController@services', 'as' => 'employees.services']);

    Route::get('employees_services/create/{employee}', ['uses' => 'Admin\EmployeeServicesController@create', 'as' => 'employees_services.create']);
    Route::post('employees_services/store', ['uses' => 'Admin\EmployeeServicesController@store', 'as' => 'employees_services.store']);
    Route::post('clients/jsonstore', ['uses' => 'Admin\ClientsController@jsonstore', 'as' => 'clientsjson.jsonstore']);
    Route::post('clients/opertorjsonstore', ['uses' => 'Admin\ClientsController@opertorjsonstore', 'as' => 'clientsjson.opertorjsonstore']);
  
    Route::get('employees_services/edit/{employees_services}', ['uses' => 'Admin\EmployeeServicesController@edit', 'as' => 'employees_services.edit']);
    Route::put('employees_services/update/{employees_services}', ['uses' => 'Admin\EmployeeServicesController@update', 'as' => 'employees_services.update']);

    Route::delete('employees_services/destroy/{employees_services}', ['uses' => 'Admin\EmployeeServicesController@destroy', 'as' => 'employees_services.destroy']);

    Route::post('employees_mass_destroy', ['uses' => 'Admin\EmployeesController@massDestroy', 'as' => 'employees.mass_destroy']);
    Route::resource('working_hours', 'Admin\WorkingHoursController');
    
    Route::get('working_hours/employeeworkinghours/{employees_id}', ['uses' => 'Admin\WorkingHoursController@employeeworkinghours', 'as' => 'working_hours.employeeworkinghours']);

    #Route::resource('leaves', 'Admin\LeaveController');
   
   Route::get('leave/leavelist/{employees_id}', ['uses' => 'Admin\LeaveController@leavelist', 'as' => 'leave.leavelist']);
   
   

    Route::get('employees_leaves/create/{employee}', ['uses' => 'Admin\LeaveController@create', 'as' => 'employees_leaves.create']);
    Route::post('employees_leaves/store', ['uses' => 'Admin\LeaveController@store', 'as' => 'employees_leaves.store']);

    Route::get('employees_leaves/edit/{employees_services}', ['uses' => 'Admin\LeaveController@edit', 'as' => 'employees_leaves.edit']);
    Route::put('employees_leaves/update/{employees_services}', ['uses' => 'Admin\LeaveController@update', 'as' => 'employees_leaves.update']);

    Route::delete('employees_leaves/destroy/{employees_services}', ['uses' => 'Admin\LeaveController@destroy', 'as' => 'employees_leaves.destroy']);

   /*Custom Timing*/

   Route::get('employeecustomtiming/employeecustomtiminglist/{employees_id}', ['uses' => 'Admin\EmployeeCustomtimingController@timinglist', 'as' => 'employeecustomtiming.employeecustomtiminglist']);
   
   

    Route::get('employees_customtiming/create/{employee}', ['uses' => 'Admin\EmployeeCustomtimingController@create', 'as' => 'employees_customtiming.create']);
    
    Route::post('employees_customtiming/store', ['uses' => 'Admin\EmployeeCustomtimingController@store', 'as' => 'employees_customtiming.store']);

    Route::get('employees_customtiming/edit/{id}', ['uses' => 'Admin\EmployeeCustomtimingController@edit', 'as' => 'employees_customtiming.edit']);
    Route::post('employees_customtiming/update', ['uses' => 'Admin\EmployeeCustomtimingController@update', 'as' => 'employees_customtiming.update']);

    Route::delete('employees_customtiming/destroy/{employees_services}', ['uses' => 'Admin\EmployeeCustomtimingController@destroy', 'as' => 'employees_customtiming.destroy']);

  /*custom Timing End*/
    Route::post('working_hours_mass_destroy', ['uses' => 'Admin\WorkingHoursController@massDestroy', 'as' => 'working_hours.mass_destroy']);
    Route::get('appointments/moneybirdauth', ['uses' => 'Admin\AppointmentsController@afterMoneybirdAuth', 'as' => 'appointments.moneybirdauth']);
    Route::resource('appointments', 'Admin\AppointmentsController');
    
    Route::resource('opertorappointments', 'Admin\OpertorappointmentController');
     

   
    Route::get('clientwithoutmoneybird', ['uses' => 'Admin\ClientsController@clientwithoutmoneybird', 'as' => 'clients.clientwithoutmoneybird']);

  Route::get('edit/clientwithoutmoneybird/{id}', ['uses' => 'Admin\ClientsController@editwithoutmoneybird', 'as' => 'clients.editwithoutmoneybird']);
  Route::put('clientwithoutmoneybird/{id}', ['uses' => 'Admin\ClientsController@updatewithoutmoneybird', 'as' => 'clients.updatewithoutmoneybird']);
  Route::delete('delete/destroywithoutmoneybird/{id}', ['uses' => 'Admin\ClientsController@destroywithoutmoneybird', 'as' => 'clients.destroywithoutmoneybird']);
  

  Route::get('showwithoutmoneybird/{id}', ['uses' => 'Admin\ClientsController@showwithoutmoneybird', 'as' => 'client.showwithoutmoneybird']);

    //withoutmoneybird

//   Route::get('clientwithoutmoneybird', 'Admin\ClientsController@withoutmoneybirdid'); 

  Route::get('appointment_cancle/{id}', ['uses' => 'Admin\AppointmentsController@cancleinv', 'as' => 'appointment_cancle']);



    Route::post('appointments_mass_destroy', ['uses' => 'Admin\AppointmentsController@massDestroy', 'as' => 'appointments.mass_destroy']);
    Route::get('appointment_destroy/{id}', ['uses' => 'Admin\AppointmentsController@destroy', 'as' => 'appointment_destroy']);
    Route::get('appointment_copy/{id}', ['uses' => 'Admin\AppointmentsController@copy', 'as' => 'appointments.copy']);
	Route::resource('services', 'Admin\ServicesController');
    Route::resource('rooms', 'Admin\RoomController');
    Route::resource('emailtemplates', 'Admin\EmailTemplateController');
    Route::resource('locations', 'Admin\LocationController');

    Route::post('send_custom_email_doctor', ['uses' => 'Admin\AppointmentsController@sendcustomemaildoctor', 'as' => 'appointments.send_custom_email_doctor']);
    Route::post('send_custom_email_doctor_confirmed', ['uses' => 'Admin\AppointmentsController@sendcustomemaildoctorconfirmed', 'as' => 'appointments.send_custom_email_doctor_confirmed']);

    Route::post('send_custom_email_confirmed', ['uses' => 'Admin\AppointmentsController@sendcustomemailconfirmed', 'as' => 'appointments.send_custom_email_confirmed']);
    Route::post('send_custom_email', ['uses' => 'Admin\AppointmentsController@sendcustomemail', 'as' => 'appointments.send_custom_email']);

    Route::post('send_custom_email_invoice_confirmed', ['uses' => 'Admin\AppointmentsController@sendcustomemailinvoiceconfirmed', 'as' => 'appointments.send_custom_email_invoice_confirmed']);
    Route::post('send_custom_email_invoice', ['uses' => 'Admin\AppointmentsController@sendcustomemailinvoice', 'as' => 'appointments.send_custom_email_invoice']);

    Route::post('services_mass_destroy', ['uses' => 'Admin\ServicesController@massDestroy', 'as' => 'services.mass_destroy']);

Route::post('changeinvoicestatusp', ['uses' => 'Admin\AppointmentsController@changeinvoicestatusp', 'as' => 'appointments.changeinvoicestatusp']);


 Route::get('appointments/pview/{id}', ['uses' => 'Admin\AppointmentsController@pview', 'as' => 'appointments.pview']);
 Route::post('appointments/sendinvoice', ['uses' => 'Admin\AppointmentsController@sendinvoice', 'as' => 'appointments.sendinvoice']);
 


   Route::get('appointments/changeinvoicestatus/{id}/{status}', ['uses' => 'Admin\AppointmentsController@changeinvoicestatus', 'as' => 'appointments.changeinvoicestatus']);
  Route::get('generatepassword', ['uses' => 'Admin\ClientsController@generatePassword', 'as' => 'clients.generatepassword']);
    
	  Route::get('get-all-contact', 'Admin\ClientsController@getallcontactsave'); 
    Route::resource('taxrate', 'Admin\TaxRateController');
    Route::resource('availability', 'Admin\AvailabilityController');
    Route::get('get-all-taxrate', 'Admin\TaxRateController@getalltaxratesave'); 

  Route::get('get-emailtemplates-rmatch', 'Admin\TaxRateController@getalltaxratesave'); 

  Route::get('rmatch/{id}', ['uses' => 'Admin\EmailTemplateController@rmatch', 'as' => 'emailtemplates.rmatch']);

    Route::get('employees_working_hour/{employee}', ['uses' => 'Admin\EmployeesController@therpistWorkinghour', 'as' => 'employees_working_hour.create']);
    Route::post('employees_working_hour', ['uses' => 'Admin\EmployeesController@therpistsaveWorkinghour', 'as' => 'employees_working_hour.store']);

    Route::resource('gcalendar', 'Admin\gCalendarController');
   Route::get('oauth', [ 'uses' => 'Admin\gCalendarController@oauth','as' => 'oauthCallback']);

    Route::get('get-autocomplete', 'Admin\ClientsController@autocompleteAdd'); 
     
 
});
