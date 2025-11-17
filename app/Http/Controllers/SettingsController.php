<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function backup_seed(BalanceService $balanceService)
    {
        $mnemonic12 = Auth::user()->phrase12;
        $words = explode(" ", $mnemonic12);
        $title = "Backup Seed";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        return view('settings.backup-seed', compact('title', 'tokens', 'totalUsd', 'totalCrypto', 'mnemonic12', 'words'));
    }
    public function checkPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($request->pin, $user->pin_hash)) {
            return response()->json(['success' => false, 'message' => 'Invalid PIN.'], 401);
        }

        return response()->json(['success' => true]);
    }
    public function change_pin_view(BalanceService $balanceService)
    {
        $title = "Change PIN";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        return view('settings.change-pin', compact('title', 'tokens', 'totalUsd', 'totalCrypto'));
    }
    public function store_new_pin(Request $request)
    {
        $oldPin = $request->oldPin;
        $newPin = $request->newPin;
        $hashedPin = Auth::user()->pin_hash;
        if (Hash::check($oldPin, $hashedPin)) {
            $user_id = Auth::user()->id;
            $user = User::find($user_id);
            $user->pin_hash = Hash::make($newPin);
            $user->save();

            return redirect()->back()->with('success_msg', 'PIN Updated Successfully');
        } else {
            return redirect()->back()->with('error_msg', 'Old PIN Mismatched!');
        }
    }
    public function faq(BalanceService $balanceService)
    {
        $title = "FAQs";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        return view('settings.faq', compact('title', 'tokens', 'totalUsd', 'totalCrypto'));
    }
    public function terms_conditions(BalanceService $balanceService)
    {
        $title = "Terms & Conditions";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        return view('settings.terms-conditions', compact('title', 'tokens', 'totalUsd', 'totalCrypto'));
    }
    public function support(BalanceService $balanceService)
    {
        $title = "Support";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        $user_id = Auth::user()->id;
        return view('settings.support', compact('title', 'tokens', 'totalUsd', 'totalCrypto', 'user_id'));
    }
    public function support_success(BalanceService $balanceService)
    {
        $title = "Support Success";
        $balanceData = $balanceService->getFilteredTokens();
        $tokens = $balanceData['tokens'];
        $totalUsd = number_format((float) $balanceData['totalUsd'], 2, '.', ',');
        $totalCrypto = number_format((float) $balanceData['totalCrypto'], 8, '.', ',');
        $user_id = Auth::user()->id;
        return view('success', compact('title', 'tokens', 'totalUsd', 'totalCrypto', 'user_id'));
    }

    public function system_cleanup()
    {
        try {
            // Clear application cache
            Cache::flush();
            
            // Clear Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return redirect()->back()->with('success_msg', 'System cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_msg', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
