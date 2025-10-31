<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PinLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        // Allow access to lock/unlock routes and forward routes
        if ($request->routeIs('lock.show') || 
            $request->routeIs('lock.unlock') || 
            $request->routeIs('lock.store') ||
            $request->routeIs('wallet.forward_to_restore_wallet') ||
            $request->routeIs('wallet.forward_to_create_wallet')) {
            return $next($request);
        }

        $timeout = 300; // 5 minutes in seconds
        $isLocked = session('locked', false);

        // If already locked, redirect to lock screen
        if ($isLocked === true) {
            session(['url.intended' => $request->fullUrl()]);
            // Force session save for file driver
            $sessionDriver = config('session.driver');
            if ($sessionDriver === 'file') {
                session()->save();
            }
            return redirect()->route('lock.show');
        }

        // IMPORTANT: We use session payload 'last_active_at' instead of sessions.last_activity
        // because Laravel automatically updates last_activity on EVERY request for session expiration.
        // We need to track when user was last ACTIVE (not just when session was touched),
        // so we store it in session payload and only update it when user successfully accesses protected routes.
        
        $lastActive = session('last_active_at');
        
        if (empty($lastActive) || !is_numeric($lastActive)) {
            // Initialize on first check - try to get from session record or use current time
            $lastActive = now()->timestamp;
            
            // Try to get from database session if driver is database
            $sessionDriver = config('session.driver');
            if ($sessionDriver === 'database') {
                try {
                    $sessionId = session()->getId();
                    $sessionRecord = DB::table('sessions')
                        ->where('id', $sessionId)
                        ->where('user_id', Auth::id())
                        ->first();
                    
                    if ($sessionRecord && isset($sessionRecord->last_activity)) {
                        $lastActive = $sessionRecord->last_activity;
                    }
                } catch (\Exception $e) {
                    // If DB query fails, use current timestamp
                    Log::warning('PinLock: Failed to query sessions table', [
                        'error' => $e->getMessage(),
                        'driver' => $sessionDriver
                    ]);
                }
            }
            
            // Set the initial last_active_at - ensure it's saved
            session(['last_active_at' => $lastActive]);
            // Force session write for file driver
            if ($sessionDriver === 'file') {
                session()->save();
            }
        }
        
        $now = now()->timestamp;
        $timeSinceLastActive = $now - (int)$lastActive;

        // Check if user has been inactive beyond timeout
        if ($timeSinceLastActive > $timeout) {
            session([
                'locked' => true,
                'url.intended' => $request->fullUrl(),
            ]);
            // Force session save for file driver
            $sessionDriver = config('session.driver');
            if ($sessionDriver === 'file') {
                session()->save();
            }
            return redirect()->route('lock.show');
        }

        // Update last active timestamp only when session is not locked
        // This allows us to track true user activity vs session auto-updates
        session(['last_active_at' => $now]);
        
        // Force session save for file driver to ensure persistence
        $sessionDriver = config('session.driver');
        if ($sessionDriver === 'file') {
            session()->save();
        }
        
        return $next($request);
    }
}
