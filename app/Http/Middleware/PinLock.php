<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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

        // $timeout = 300; // seconds, or higher for production
        $timeout = 300;
        
        // Get last active timestamp with proper fallback - use session() helper for compatibility
        $lastActive = session('last_active_at');
        if (empty($lastActive)) {
            $lastActive = now()->timestamp;
            session(['last_active_at' => $lastActive]);
        }
        
        $now = now()->timestamp;
        $isLocked = session('locked', false);

        // If session is locked or last activity exceeded timeout
        if (($now - (int)$lastActive) > $timeout || $isLocked === true) {
            session([
                'locked' => true,
                'url.intended' => $request->fullUrl(),
            ]);
            
            return redirect()->route('lock.show');
        }

        // Update last active timestamp
        session(['last_active_at' => $now]);
        
        return $next($request);
    }
}
