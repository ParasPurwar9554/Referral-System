<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserDataController extends Controller
{
    public function getUserData(Request $request){
        $user_data = User::all()->first()->toArray();
        //$this->a($user_data);
       // \App\Events\SendNotification::dispatch($user_data);
    } 

    public function a($data){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    } 
}
