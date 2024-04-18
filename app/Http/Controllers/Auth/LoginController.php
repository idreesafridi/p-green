<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('home');
        } else {
            return redirect()->route('welcome');
        }
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['prompt' => 'consent'])->redirect();
    }


    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
    
        // $user->getId()
        // $user->getName()
        



        $check = User::where('email',$user->getEmail())->first();
     
        if($check){
            Auth::login($check); // Log in the user

        if ($check->hasRole('admin')) {
            
            return redirect()->route('home');
        } else {
            return redirect()->route('welcome');
        }
        }
        else
        {
            // return redirect()->route('home')->with('error','Attualmente non hai accesso al portale. Ti preghiamo di richiedere la registrazione a un amministratore.'); 
            return redirect()->route('login')->with('error', 'Attualmente non hai accesso al portale. Ti preghiamo di richiedere la registrazione a un amministratore.'); 

        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
