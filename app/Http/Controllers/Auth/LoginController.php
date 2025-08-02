<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    
     protected $redirectTo =RouteServiceProvider::Admin;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function username()
    {
        return 'phone';
    }
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    //  public function login(Request $request)  {
    
    //   $request->validate([
    //         'login_id'      =>      'required',
    //         'password'      =>      'required'
    //   ]);
     
    //   if(filter_var($request->login_id,FILTER_VALIDATE_EMAIL)){
    //         $login  =   'email';
    //   }
    //   elseif(filter_var((int)$request->login_id,FILTER_VALIDATE_INT)){
    //         $login  =   'phone';
    //   }else{
    //         $login  =   'name';
    //   }
      
    //   $request->merge([
    //         $login  =>  $request->login_id
    //   ]);
    //   if(Auth::attempt($request->only($login,'password'),$request->filled('remember'))){
    //         if(Auth::user()->type== 'superadministrator' || Auth::user()->type =='admin' || Auth::user()->type =='emp'){
    //             return redirect()->route('admin.index');
    //         }
    //         else {
    //             return redirect()->route('home');
    //         }
    //   }
    //   else{

    //         return redirect()->route('login')->withErrors(__('site.invalid_credentials'));
    //   }
    // } 
    
    /*d by mohammed*/
    public function logout(Request $request)
    {
        Auth::logout();

        // Redirect to a specific route after logout
        return redirect()->route('login');
    }

}
