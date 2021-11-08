<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/home';
    protected $lockoutTime;
    protected $auth; 
    /**
     * maxLoginAttempts
     *
     * @var
     */
    protected $maxLoginAttempts;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
         
    }
  public function maxAttempts()
    {
        return 4;
    }

  /*  public function decayMinutes()
    {
        return 1;
    }*/
     protected function validateLogin(Request $request)
    {
      if(session()->get('login.attempts') > 3)
      {
         $this->validate($request, [
            $this->username() => 'required', 
            'password' => 'required',
            'captcha' => 'required|captcha'
            // new rules here
        ]);

      }
      else
      {
         $this->validate($request, [
            $this->username() => 'required', 
            'password' => 'required',
            // new rules here
        ]);
      }
        
    }
   protected function sendFailedLoginResponse(Request $request)
        {
            $attempts = session()->get('login.attempts', 0); // get attempts, default: 0
            session()->put('login.attempts', $attempts + 1); // increase attempts

            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }
        protected function authenticated(Request $request, $user)
        {
            session()->forget('login.attempts'); // clear attempts

            if ($user->role_id == 2) 
                { return redirect('/admin/opertorappointments/create'); } 
            else 
                { return redirect('/admin/home'); }

            
        }
}
