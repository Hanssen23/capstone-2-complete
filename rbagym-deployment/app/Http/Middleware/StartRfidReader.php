<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RfidReaderService;
use Illuminate\Support\Facades\Log;

class StartRfidReader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only start RFID reader if user is authenticated and RFID hasn't been started yet
        if (auth()->check() && !session('rfid_reader_started')) {
            try {
                // Set session flag to prevent multiple attempts
                session(['rfid_reader_started' => true]);
                
                // Start RFID reader asynchronously to avoid blocking the request
                $this->startRfidReaderAsync();
                
            } catch (\Exception $e) {
                Log::error('Failed to start RFID reader via middleware: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
    
    /**
     * Start RFID reader asynchronously
     */
    private function startRfidReaderAsync()
    {
        try {
            $pythonPath = 'C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe';
            $scriptPath = base_path('rfid_reader.py');
            
            if (!file_exists($scriptPath)) {
                Log::error('RFID reader script not found at: ' . $scriptPath);
                return;
            }
            
            // Start the process in background using PowerShell
            $command = sprintf(
                'powershell -Command "Start-Process -FilePath \'%s\' -ArgumentList \'%s\' -WindowStyle Hidden"',
                $pythonPath,
                $scriptPath
            );
            
            // Execute command without waiting for completion
            exec($command . ' >nul 2>&1 &');
            
            Log::info('RFID reader started asynchronously via middleware');
            
        } catch (\Exception $e) {
            Log::error('Failed to start RFID reader asynchronously: ' . $e->getMessage());
        }
    }
}
