<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\Service;
use App\WorkingHour;
use Carbon\Carbon;
class Createemployeschedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create employee schedule per week';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $to_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+30 days'));
        $lookup=['Sunday'=>0,'Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6];
       $pickup_dates = [];
        $today = Carbon::today()->toDateString();
        for ($i = 0; $i < 30; $i++) {
            $pickup_dates[]=Carbon::parse($today);
              $today = Carbon::parse($today)->addDay()->toDateString();
        }
        $currentday = date('l'); //Monday or tuesdays
         
       $employee = Employee::all(); 
       
        foreach($employee as $employe)
               {
                   $employee_id = $employe->id;
                    foreach($pickup_dates as $pickup_date)
                       {
                          $working_hours_date = date('Y-m-d',strtotime($pickup_date));

                          $workingHours = WorkingHour::where('employee_id','=',$employee_id)->where('date','=',$working_hours_date)->get();
                          $workingHourCount = $workingHours->count();

                            if($workingHourCount == 0)
                                {
                                  $dayL = date('l', strtotime($pickup_date));
                                   
                                  $workingHoursDays = WorkingHour::where('employee_id','=',$employee_id)->where('days','=',$dayL)->get(); 

                                  if($workingHoursDays->count() > 0)
                                     {

                                         $working_hour = WorkingHour::create([
                                           'employee_id' => $employee_id,
                                           'date' =>  $working_hours_date,
                                           'start_time' => $workingHoursDays[0]->start_time,
                                           'finish_time' => $workingHoursDays[0]->finish_time,
                                           'repeate' => $workingHoursDays[0]->repeate,
                                           'days' => $workingHoursDays[0]->days,
                                           'location_id' => $workingHoursDays[0]->location_id
                                         ]);
                                     }
                                }
                        }
                }
    }
}
