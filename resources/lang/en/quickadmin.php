<?php
return [	'company' => [
		'title' => 'Company',
		'created_at' => 'Time',
		'fields' => [
			'name' => 'Name',
			'email' => 'Email',
			'password' => 'Password',
			'role' => 'Role',
			'remember-token' => 'Remember token',
		],
	],	'user-management' => [		'title' => 'User Management',		'created_at' => 'Time',		'fields' => [		],	],
    'roles' => [		'title' => 'Roles',		'created_at' => 'Time',		'fields' => [			'title' => 'Title',		],	],
    'users' => [		'title' => 'Users',		'created_at' => 'Time',		'fields' => [			'name' => 'Name',
        'email' => 'Email',			'password' => 'Password',			'role' => 'Role',			'remember-token' => 'Remember token',		],	],
    'clients' => [		'title' => 'Customer',		'created_at' => 'Time',		'fields' => [			'first-name' => 'First name',
        'last-name' => 'Last name',            'postcode' => 'Postcode',            'house_number' => 'House Number',            'address' => 'Address',
        'dob' => 'Date Of Birth',			'phone' => 'Phone',			'email' => 'Email',		],	],		'employees' => [		'title' => 'Therapist',
        'created_at' => 'Time',		'fields' => [			'first-name' => 'First name',			'last-name' => 'Last name',			'phone' => 'Phone',			'email' => 'Email', 		],	],		'employees-service' => [		'title' => 'Therapist Services',		'created_at' => 'Time',		'fields' => [			'employee' => 'Employee',			'service' => 'Service',			'price' => 'Price',			'weekend_price' => 'Weekend Price',			'discount' => 'Discount',			'tax' => 'Tax',		],	],	'services' => [		'title' => 'Therapies',		'created_at' => 'Time',		'fields' => [			'name' => 'Name', 		],	],			'working-hours' => [		'title' => 'Working hours',		'created_at' => 'Time',		'fields' => [			'employee' => 'Employee',			'date' => 'Date',			'start-time' => 'Start time',			'finish-time' => 'Finish time',		],	],		'appointments' => [		'title' => 'Booking',		'created_at' => 'Time',		'invoice_paid' => 'Invoice Paid',		'invoice_unpaid' => 'Invoice UnPaid',		'fields' => [			'client' => 'Client',			'employee' => 'Employee',			'start-time' => 'Start time',			'finish-time' => 'Finish time',			'comments' => 'Comments',			'moneybird_status' => 'Moneybird Status',			'status' => 'Status',		],	],	'rooms' => [		'title' => 'Room',		'created_at' => 'Time',		'fields' => [			'name' => 'Room Name',			'location'=>'Location Name'		],	],	'locations' => [		'title' => 'Location',		'created_at' => 'Time',		'fields' => [			'name' => 'location Name',		],	],	'leaves' => [		'title' => 'Leave',		'created_at' => 'Time',		'fields' => [			'name' => 'Leave Title',			'leave_date' => 'Leave Date',			'employee_id' => 'Employee',			'leave_comment'=>'Comments'		],	],	'customtiming' => [		'title' => 'Custom Timeing',		'created_at' => 'Time',		'fields' => [			'date' => 'Date',			'start-time' => 'Start time',			'finish-time' => 'Finish time',			'location-id' => 'Location',		],	],	'emailtemplates' => [		'title' => 'Email Template', 		'created_at' => 'Time',		'fields' => [			'subject' => 'Subject',			'content' => 'Email Content',			'email_type' => 'Email Type',		],	],	'qa_create' => 'Create',	'qa_save' => 'Save',	'qa_edit' => 'Edit',	'qa_view' => 'View',	'qa_update' => 'Update',	'qa_list' => 'List',	'qa_no_entries_in_table' => 'No entries in table',	'custom_controller_index' => 'Custom controller index.',	'qa_logout' => 'Logout',
    'qa_add_new' => 'Add new', 'qa_are_you_sure_new' => 'Are you sure? This will delete not only therapist but also all bookings of therapist.'	,'qa_are_you_sure' => 'Are you sure?',	'qa_back_to_list' => 'Back to list',
    'qa_dashboard' => 'Dashboard',	'qa_delete' => 'Delete',	'qa_service'=> 'Services',	'qa_working_hours'=> 'Working hours',
    'quickadmin_title' => 'Appointments',	'qa_leave'=> 'Leaves',	'qa_custom_timing'=> 'Date Wise Custom Timing',];