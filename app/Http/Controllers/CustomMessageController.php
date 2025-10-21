<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BalanceService;

class CustomMessageController extends Controller
{
    public function alerts(BalanceService $balanceService)
    {
        $title = "Support";
        $tokens = $balanceService->getFilteredTokens();
        return view('custom-messages.alerts', compact('title', 'tokens'));
    }

    public function announcements(BalanceService $balanceService)
    {
        $title = "Support";
        $tokens = $balanceService->getFilteredTokens();
        return view('custom-messages.announcements', compact('title', 'tokens'));
    }
}
