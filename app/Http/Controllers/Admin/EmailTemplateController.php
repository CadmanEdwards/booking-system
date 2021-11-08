<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\EmailTemplate;
use App\TblCompanies;
use App\Roles;
use Validator;
use Illuminate\Support\Facades\Input;


class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

         if (! Gate::allows('emailtemplate_access')) {
            return abort(401);
        }
 
        $emailtemplates = EmailTemplate::orderBy('email_type','desc')->orderBy('id','asc')->get();
        return view('admin.emailtemplates.index', compact('emailtemplates') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
      if (! Gate::allows('emailtemplate_create')) {
            return abort(401);
        }
        $relations = [
            'email_user_type' => \App\Role::whereIn('id', array(3,4,5,6))->get()->pluck('title', 'id')
        ];

   //   echo "<pre>"; print_r($relations); die;

        return view('admin.emailtemplates.create',$relations);
        
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
           if (! Gate::allows('emailtemplate_create')) {
            return abort(401);
        }
        
       // $rules = array( 'flie_name' => '');
      
    /*  $validator = Validator::make(Input::all(), $rules);

// Validate the input and return correct response
  if ($validator->fails())
  {
      return response()->json(array(
          'success' => false,
          'errors' => $validator->getMessageBag()->toArray()

      ), 200); 
  }*/
     if(!empty($request->file('attachment')))
     {
        $image = $request->file('attachment');
        $input['attachment'] = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/upload');
        $image->move($destinationPath, $input['attachment']);
        
     }
        //$input['imagename'] 
        $input['email_content']= $request->email_content;
        $input['email_subject']= $request->email_subject;
        $input['email_user_type']= $request->email_user_type;
        $input['email_id']= !empty($request->email_id) ? $request->email_id : '';
        //$input['attachment']= $request->file('email_content');

       
         $emailTemplate = EmailTemplate::create($input); 
        
        return redirect()->route('admin.emailtemplates.index');
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
    public function rmatch($id)
    {
        
        if (! Gate::allows('emailtemplate_edit')) {
            return abort(401);
        }
        $emailtemplates = EmailTemplate::findOrFail($id);
        $oldval = $emailtemplates->attachment;
        $emailtemplates->attachment = '';
        $destinationPath = public_path('/upload/'.$oldval);
        $emailtemplates->update();
        if(file_exists($destinationPath)){ unlink($destinationPath); }
        return redirect()->back()->with('success', 'Attachment deleted successfully!');
    
    }

    public function edit($id)
    {
        //

        if (! Gate::allows('emailtemplate_edit')) {
            return abort(401);
        }
        $emailtemplates = EmailTemplate::findOrFail($id);
        $company = TblCompanies::findOrFail(1);
        $invoice_template = $company->invoice_template;
        //$location = $location->prepend($location->rooms);
         $relations = [
            'email_user_type' => \App\Role::whereIn('id', array(3,4,5,6))->get()->pluck('title', 'id'),
            'invoice_template'=>$invoice_template
        ];
        return view('admin.emailtemplates.edit', compact('emailtemplates')+$relations);
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
        if (! Gate::allows('emailtemplate_edit')) {
            return abort(401);
        }
        $emailtemplates = EmailTemplate::findOrFail($id);
        $emailtemplates->email_content = $request->email_content;
        if(($id>22 and $id<27) && !empty($request->email_content_h))
            { $emailtemplates->email_content_h = $request->email_content_h; }
        
        $emailtemplates->email_subject = $request->email_subject;
        $emailtemplates->email_user_type = $request->email_user_type;
        $emailtemplates->email_id = $request->email_id;

        if(!empty($request->file('attachment')))
         {
             $image = $request->file('attachment');
            $input['attachment'] = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload');
            $image->move($destinationPath, $input['attachment']);
            $emailtemplates->attachment = $input['attachment'];     
         }
        $emailtemplates->update();

        //$emailtemplates->update($request->all()); 


        return redirect()->route('admin.emailtemplates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */ 
    public function destroy($id)
    {
        
        if (! Gate::allows('emailtemplate_delete')) {
            return abort(401);
        }
       
        $EmailTemplate = EmailTemplate::findOrFail($id);
        $EmailTemplate->forceDelete();
        return redirect()->route('admin.emailtemplates.index');

        
        
    
    }
}
