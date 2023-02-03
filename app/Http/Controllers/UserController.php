<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Network;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
  public function loadRegister(Request $request)
  {
    return view('register');
  }

  public function registered(Request $request)
  {
    $request->validate([
      'name' => 'required|string|min:2',
      'email' => 'required|string|email|max:100|unique:users',
      'password' => 'required|same:confirm_password'
    ]);

    $referralCode = Str::random(10);
    $token = Str::random(50);

    if (isset($request->referral_code)) {
      $userData = User::where('referral_code', $request->referral_code)->get();
      if (count($userData) > 0) {
        $user_id = User::insertGetId([
          'name' => $request->name,
          'email' => $request->email,
          'password' => Hash::make($request->password),
          'referral_code' => $referralCode,
          'remember_token' => $token
        ]);
        Network::insert([
          'referral_code' => $request->referral_code,
          'user_id' => $user_id,
          'parent_user_id' => $userData[0]['id']
        ]);
      } else {
        return back()->with('error', 'Please enter valid referral code!');
      }
    } else {
      User::insert([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'referral_code' => $referralCode,
        'remember_token' => $token
      ]);
    }
    $domain =  URL::to('/');
    $url = $domain . '/referral-register?ref=' . $referralCode;
    $data['url'] = $url;
    $data['name'] = $request->name;
    $data['email'] = $request->email;
    $data['password'] = $request->password;
    $data['title'] = "Registered";

    Mail::send('emails.registerMail', ['data' => $data], function ($message) use ($data) {
      $message->to('paraspurwar5@gmail.com')->subject($data['title']);
    });

    // Verification mail send
    $url = $domain . '/email-verification/' . $token;
    $data['url'] = $url;
    $data['name'] = $request->name;
    $data['email'] = $request->email;
    $data['title'] = "Referral Verification Mail";

    Mail::send('emails.verifyMail', ['data' => $data], function ($message) use ($data) {
      $message->to('test@gmail.com')->subject($data['title']);
    });

    return back()->with('success', 'Your registraion has been successfull & Please verify your email!');
  }

  public function loadRefferralRegister(Request $request)
  {
    if (isset($request->ref)) {
      $referral_code = $request->ref;
      $user_data = User::where('referral_code', $referral_code)->get();
      if (count($user_data) > 0) {
        return view('referralRegister', compact('referral_code'));
      } else {
        return view('404');
      }
    } else {
      return  redirect('/');
    }
  }

  public function emailVerification($token)
  {
    $user_data =  User::where('remember_token', $token)->get();
    if (count($user_data) > 0) {
      if ($user_data[0]['is_verified'] == 1) {
        return view('verified', ['message' => 'Your mail is already verified!']);
      }

      User::where('id', $user_data[0]['id'])->update([
        'is_verified' => 1,
        'email_verified_at' => date('Y-m-d H:i:s')
      ]);
      return view('verified', ['message' => 'Your ' . $user_data[0]['email'] . ' mail verified successfully.']);
    } else {
      return view('verified', ['message' => '404 Page Not Found!']);
    }
  }

  public function loadLogin()
  {
    return view('login ');
  }

  public function userLogin(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required'
    ]);

    $user_data = User::where('email', $request->email)->first();

    if (!empty($user_data)) {
      if ($user_data->is_verified == 0) {
        return back()->with('error', 'Please verify your email!');
      }
    }

    $user_credentials = $request->only('email', 'password');
    if (Auth::attempt($user_credentials)) {
      return redirect('/dashboard');
    } else {
      return back()->with('error', 'Username & Password is incorrect!');
    }
  }

  public function loadDashboard()
  {
    $networkCount = Network::where('parent_user_id',Auth::user()->id)->orwhere('user_id',Auth::user()->id)->count();
    $networkData = Network::with('user')->where('parent_user_id',Auth::user()->id)->get();

     $socialShare = \Share::page(URL::to('/').'/referral-register?ref='.Auth::user()->referral_code,'Share and Earn Points by Referral Links')
     ->facebook()
     ->twitter()
     ->linkedin('Extra linkedin summary can be passed here')
     ->whatsapp();
  

    return view('dashboard',compact(['networkCount','networkData','socialShare']));


  }

  public function logout(Request $request)
  {
    $request->session()->flush();
    Auth::logout();
    return redirect('/');
  }

  public function referralTrack(){
        $dateLabels = [];
        $dateData = [];
        for($i = 30; $i >= 0; $i--){
           $dateLabels[] = Carbon::now()->subDays($i)->format('d-m-Y');
           $dateData[] =  Network::whereDate('created_at',Carbon::now()->subDays($i)->format('Y-m-d'))
           ->where('parent_user_id',Auth::user()->id)->count();
        }
    
        $dateLabels = json_encode($dateLabels);
        $dateData = json_encode($dateData);
        return view('referralTrack',compact(['dateLabels','dateData']));
  }
  
 public function deleteAccount(Request $request){

    try {
      User::where('id',Auth::user()->id)->delete();
      $request->session()->flush();
      Auth::logout();
     return response()->json(['success'=>true]);
    } catch (\Exception $e) {
     return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
    }

 } 


}
