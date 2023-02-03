<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SMSController extends Controller
{
    public function sendSMS(Request $request){
         try {
           $account_sid = env('TWILIO_SID');
           $account_token = env('TWILIO_TOKEN');
           $number = env('TWILIO_FROM');
           $client = new Client($account_sid, $account_token);
           $client->messages->create(
              '+91'.$request->number, // Text this number
            [
             'from' => $number, // From a valid Twilio number
             'body' => $request->message
            ]
          
         );
      
//print $message->sid;
return 'message sent';
         } catch (\Throwable $e) {
           return $e->getMessage();
         }
    }
}
