<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Jobs\SingleEmailJob;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function verifyUser($uuid) 
    {
        $userObj = User::whereUuid($uuid)->firstOrFail();
        if ($userObj && $userObj->email_verified_at == null) {
            $emailDataObj = EmailTemplate::where('slug', config('constant.under_review'))->first();
            if (isset($emailDataObj)) {
                
                $file = 'email.profile_under_review';
                $usersData = $userObj;
                $randomPass = $this->generateRandomString();
                $userObj->password = Hash::make($randomPass);
                $userObj->save();
                $emailData = [
                    'display_name' => $userObj->first_name. ' '.$userObj->last_name ,
                    'content' =>   $emailDataObj->body,
                    'random_password' => $randomPass,
                ];
                $sub =  $emailDataObj->subject;
                $this->singleUserEmailNotifiaction($emailData, $sub, $usersData ,$file);
            }
            $userObj->update(['email_verified_at' => now()]);
            $response = true;
        } else {
            $response = false;
        }
        return view('frontend.verify_user',['response' => $response]);
    }

    public function singleUserEmailNotifiaction($emailData, $sub, $users ,$file)
    {
        dispatch(new SingleEmailJob($emailData, $sub, $users ,$file))->delay(now()->addSeconds(30));
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
