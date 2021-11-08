<?php

function getEmployeeId($id) {
	$data = \DB::table('employees')
        ->select('id')
        ->where('user_id', $id)
        ->whereNull('deleted_at')
        ->first();

    return $data->id;
}

function getUsername($id)
 {
    $data = \DB::table('users')
        ->select('id','name')
        ->where('id', $id)
        ->first();
     $name='';
     
     if(isset($data->name) )
      {
       
        $name = $data->name;
      }
     
    return $name;

 }
 function isClientVerified($id)
 {
    $data = \DB::table('clients')
        ->select('id','email_verified')
        ->where('id', $id)
        ->first();
        $email_verified = 0;
        if(isset($data->email_verified))
         $email_verified = $data->email_verified;
     
     
    return $email_verified;   
 }
function getParentDetails($id) {
	$data = \DB::table('clients')
        ->select('id','first_name','last_name')
        ->where('id', $id)
        ->whereNull('deleted_at')
        ->first();
     $name='';
     
     if(isset($data->first_name) || isset($data->last_name))
      {
      	$fname = isset($data->first_name) ? $data->first_name : '';
      	$lname = isset($data->last_name) ? $data->last_name : '';
        $name =  $fname .' '. $lname;
      }
     
    return $name;
}

function getPageTitle($id) {
  $data = \DB::table('pages')
        ->select('page_subject')
        ->where('id', $id)
        ->whereNull('deleted_at')
        ->first();

    return $data->page_subject;
}

function getAllPages() {
  $data = \DB::table('pages')
        ->select('*')
        ->whereNull('deleted_at')
        ->first();

    return $datas;
}