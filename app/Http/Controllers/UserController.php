<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function onboarding1()
    {
        return view('guest.onboarding1');
    }
    public function onboarding2()
    {
        return view('guest.onboarding2');
    }
    public function onboarding3()
    {
        return view('guest.onboarding3');
    }
}
