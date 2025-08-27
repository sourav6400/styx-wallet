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
        $title = "Welcome";
        return view('guest.onboarding1', compact('title'));
    }
    public function onboarding2()
    {
        $title = "Welcome";
        return view('guest.onboarding2', compact('title'));
    }
    public function onboarding3()
    {
        $title = "Welcome";
        return view('guest.onboarding3', compact('title'));
    }
}
