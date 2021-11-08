<?php
namespace App\Http\Controllers\Admin;


use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Http\Request;
use App\Appointment;
use App\EmailTemplate;
use App\Client;
use App\Employee;
use App\Service;
use App\Location;
use App\Room;
use DB;
class gCalendarController extends Controller
{
    protected $client;

    public function __construct()
    {
    
       /* 
        $client = new Google_Client();
$client->setAuthConfig(public_path().'/client_secret_1053230855236-974tr0f3u72hmdv52a680vee1svffc1g.apps.googleusercontent.com.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $this->client = $client;*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session_start();
        //echo "<pre>";print_r($_SESSION['access_token']);exit;
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
         $this->InsertedAppointment();
            //$results = $service->events->listEvents($calendarId);
            //return $results->getItems();

        } else {
           
            return redirect()->route('admin.oauthCallback');
        }

    }

    public function oauth()
    {
        session_start();
       // echo "sharad";exit;
        $rurl = action('Admin\gCalendarController@oauth');
        $this->client->setRedirectUri($rurl);
        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            $this->InsertedAppointment();
                 //return redirect()->route('cal.index');
            

            }
            
                  
        
    }
  public function InsertedAppointment()
  {
    $allappointments = Appointment::join('clients', 'appointments.client_id', '=', 'clients.id')->join('locations', 'appointments.location_id', '=', 'locations.id')->
           join('employees', 'appointments.employee_id', '=', 'employees.id')
           ->join('services', 'services.id', '=', 'appointments.service_id')->select('appointments.id as appointmentId','clients.first_name','clients.last_name','employees.email as therapist_email','clients.email as customeremail','appointments.room_id','locations.location_name','appointments.start_time','appointments.finish_time','employees.first_name AS emp_f_name','employees.last_name AS emp_l_name','services.name as service_name','employees.small_info','clients.phone as cphone','appointments.room_id')->where('appointments.synced','=','N')->
where('appointments.deleted_at','=',NULL)->orderBy('appointmentId', 'desc')->limit(700)->get(); 
            
//echo "<pre>";print_r($allappointments);exit;
        $retunDatastring = " ";    
            foreach ($allappointments as $allappointment) {
            
            $cphone = $allappointment->cphone;
            $room_id = $allappointment->room_id;
            $roomArr = DB::table('rooms')->where('id','=',$room_id)->get();
            $room_name = "";
            if(count($roomArr)>0)
            { $room_name = $roomArr[0]->room_name;}
            $customeremail = $allappointment->customeremail;
            $therapist_email = $allappointment->therapist_email;
            $appointmentId = $allappointment->appointmentId;
            $employee_fname = $allappointment->emp_f_name;
            $employee_lname = $allappointment->emp_l_name;
            $client_fname = $allappointment->first_name;
            $client_lname = $allappointment->last_name;
            $service_name = $allappointment->service_name;
            $location_name = $allappointment->location_name;
            $clientName = $client_fname." ".$client_lname;
            
            $therapistName = $employee_fname." ".$employee_lname;

            $summary = $clientName." appointment with ".$therapistName;
           
            $description_older = $clientName." appointment with ".$therapistName." for ".$service_name." and client phone no ".$cphone;

            $description = $clientName." appointment with ".$therapistName." at ".$location_name;


            $startDateTimeArray = explode(" ", $allappointment->start_time);
            $endDateTimeArray = explode(" ",$allappointment->finish_time);
            
            $startDateTime=$startDateTimeArray[0]."T".$startDateTimeArray[1];
            $endDateTime=$endDateTimeArray[0]."T".$endDateTimeArray[1];
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $summary,
                'description' => $description,
                'start' => ['dateTime' => $startDateTime,'timeZone' => 'Europe/Amsterdam'],
                'end' => ['dateTime' => $endDateTime,'timeZone' => 'Europe/Amsterdam'],
                'reminders' => ['useDefault' => true],

            ]);
            
            $results = $service->events->insert($calendarId, $event);
           $retunDatastring .="Appointment Id ".$appointmentId." description ".$description; 

           DB::table('appointments')
            ->where('id', $appointmentId)
            ->update(['synced' => 'Y']);

       }
       echo $retunDatastring;
  }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendar.createEvent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session_start();
        $startDateTime = $request->start_date;
        $endDateTime = $request->end_date;

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $request->title,
                'description' => $request->description,
                'start' => ['dateTime' => $startDateTime],
                'end' => ['dateTime' => $endDateTime],
                'reminders' => ['useDefault' => true],
            ]);
            $results = $service->events->insert($calendarId, $event);
            if (!$results) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($eventId)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);

            $service = new Google_Service_Calendar($this->client);
            $event = $service->events->get('primary', $eventId);

            if (!$event) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $event]);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $eventId)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $startDateTime = Carbon::parse($request->start_date)->toRfc3339String();

            $eventDuration = 30; //minutes

            if ($request->has('end_date')) {
                $endDateTime = Carbon::parse($request->end_date)->toRfc3339String();

            } else {
                $endDateTime = Carbon::parse($request->start_date)->addMinutes($eventDuration)->toRfc3339String();
            }

            // retrieve the event from the API.
            $event = $service->events->get('primary', $eventId);

            $event->setSummary($request->title);

            $event->setDescription($request->description);

            //start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDateTime);
            $event->setStart($start);

            //end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endDateTime);
            $event->setEnd($end);

            $updatedEvent = $service->events->update('primary', $event->getId(), $event);


            if (!$updatedEvent) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $updatedEvent]);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($eventId)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $service->events->delete('primary', $eventId);

        } else {
            return redirect()->route('oauthCallback');
        }
    }
}
