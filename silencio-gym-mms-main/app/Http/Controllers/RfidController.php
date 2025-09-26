<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Attendance;
use App\Models\ActiveSession;
use App\Models\RfidLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RfidController extends Controller
{
    /**
     * Handle RFID card tap for check-in/check-out
     */
    public function handleCardTap(Request $request): JsonResponse
    {
        $request->validate([
            'card_uid' => 'required',
            'device_id' => 'nullable|string',
        ]);

        $cardUid = $request->input('card_uid');
        $deviceId = $request->input('device_id', 'main_reader');
        
        // Check if RFID system is online (Python process running)
        if (!$this->isRfidReaderRunning()) {
            $this->logRfidEvent($cardUid, 'card_tap', 'failed', 
                "RFID system is offline - tap ignored", $deviceId);
            
            return response()->json([
                'success' => false,
                'message' => 'RFID system is offline. Please start the RFID reader.',
                'action' => 'system_offline',
                'feedback' => [
                    'message' => 'System offline',
                    'sound' => 'error'
                ]
            ], 503);
        }

        // Prevent test scripts from creating sessions in production
        if ($deviceId === 'test_device') {
            Log::info('Test device detected, skipping session creation', [
                'card_uid' => $cardUid,
                'device_id' => $deviceId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Test device not allowed in production',
                'action' => 'test_device_blocked'
            ], 403);
        }

        try {
            // Use a shorter timeout for database operations
            DB::beginTransaction();
            
            // Find member by UID with lock for update to prevent conflicts
            $member = Member::where('uid', $cardUid)->lockForUpdate()->first();

            if (!$member) {
                $this->logRfidEvent($cardUid, 'unknown_card', 'failed', 
                    "Unknown card UID: {$cardUid}", $deviceId);
                
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Unknown card. Please contact staff.',
                    'action' => 'unknown_card',
                    'card_uid' => $cardUid,
                    'feedback' => [
                        'message' => 'Card not registered',
                        'sound' => 'error'
                    ]
                ], 404);
            }

            // Check if member is a valid member (not expired)
            if ($member->membership_expires_at && $member->membership_expires_at < now()) {
                $this->logRfidEvent($cardUid, 'check_in', 'failed', 
                    "Member {$member->first_name} {$member->last_name} has expired membership", $deviceId);
                
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Membership has expired. Please renew.',
                    'action' => 'expired_membership',
                    'feedback' => [
                        'message' => 'Membership expired',
                        'sound' => 'error'
                    ]
                ], 403);
            }

            // Check if member has an active session
            $activeSession = ActiveSession::where('member_id', $member->id)
                ->where('status', 'active')
                ->first();

            if ($activeSession) {
                // Member is checking out
                return $this->handleCheckOut($member, $activeSession, $deviceId);
            } else {
                // Member is checking in
                return $this->handleCheckIn($member, $deviceId);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RFID Error: ' . $e->getMessage(), [
                'card_uid' => $cardUid,
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            $this->logRfidEvent($cardUid, 'check_in', 'failed', 
                "System error: " . $e->getMessage(), $deviceId);

            return response()->json([
                'success' => false,
                'message' => 'System error. Please try again.',
                'action' => 'system_error'
            ], 500);
        }
    }

        /**
     * Handle member check-in
     */
    private function handleCheckIn(Member $member, string $deviceId): JsonResponse
    {
        try {
            // Check if member has any existing active session and deactivate it
            $existingActiveSession = ActiveSession::where('member_id', $member->id)
                ->where('status', 'active')
                ->first();
                
            if ($existingActiveSession) {
                // Deactivate the existing session
                $existingActiveSession->update([
                    'status' => 'inactive',
                    'check_out_time' => now(),
                    'session_duration' => $existingActiveSession->currentDuration,
                ]);
                
                // Update the attendance record
                $existingActiveSession->attendance->update([
                    'check_out_time' => now(),
                    'status' => 'checked_out',
                    'session_duration' => $existingActiveSession->currentDuration,
                ]);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'member_id' => $member->id,
                'check_in_time' => now(),
                'status' => 'checked_in',
            ]);

            // Create active session
            $activeSession = ActiveSession::create([
                'member_id' => $member->id,
                'attendance_id' => $attendance->id,
                'check_in_time' => now(),
                'status' => 'active',
            ]);

            // Update member status to active (they're currently in the gym)
            $member->update(['status' => 'active']);

            // Log successful check-in
            $this->logRfidEvent($member->uid, 'check_in', 'success', 
                "Member {$member->first_name} {$member->last_name} checked in successfully", $deviceId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Welcome back, {$member->first_name} {$member->last_name}!",
                'action' => 'check_in',
                'member' => [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'member_number' => $member->member_number,
                    'email' => $member->email,
                    'membership_plan' => $member->membershipPlan?->name ?? 'Unknown',
                    'membership_status' => ucfirst($member->status),
                    'check_in_time' => now()->format('h:i:s A'),
                ],
                'feedback' => [
                    'message' => 'Check-in successful',
                    'sound' => 'success'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Check-in Error: ' . $e->getMessage(), [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Check-in failed. Please try again.',
                'action' => 'check_in_error'
            ], 500);
        }
    }

    /**
     * Handle member check-out
     */
    private function handleCheckOut(Member $member, ActiveSession $activeSession, string $deviceId): JsonResponse
    {
        try {
            // Calculate session duration in minutes
            $sessionDurationMinutes = $activeSession->check_in_time->diffInMinutes(now());
            $sessionDurationText = $activeSession->currentDuration;
            
            // Update attendance record
            $attendance = $activeSession->attendance;
            $attendance->update([
                'check_out_time' => now(),
                'status' => 'checked_out',
                'session_duration' => $sessionDurationText,
            ]);

            // Update active session
            $activeSession->update([
                'status' => 'inactive',
                'check_out_time' => now(),
                'session_duration' => $sessionDurationText,
            ]);

            // Update member status to offline (they're no longer in the gym)
            $member->update(['status' => 'offline']);

            // Log successful check-out
            $this->logRfidEvent($member->uid, 'check_out', 'success', 
                "Member {$member->first_name} {$member->last_name} checked out. Session duration: {$sessionDurationText}", $deviceId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Goodbye, {$member->first_name} {$member->last_name}! Session duration: {$sessionDurationText}",
                'action' => 'check_out',
                'member' => [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'member_number' => $member->member_number,
                    'email' => $member->email,
                    'membership_plan' => $member->membershipPlan?->name ?? 'Unknown',
                    'membership_status' => ucfirst($member->status),
                    'check_out_time' => now()->format('h:i:s A'),
                    'session_duration' => $sessionDurationText,
                ],
                'feedback' => [
                    'message' => 'Check-out successful',
                    'sound' => 'success'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Check-out Error: ' . $e->getMessage(), [
                'member_id' => $member->id,
                'session_id' => $activeSession->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Check-out failed. Please try again.',
                'action' => 'check_out_error'
            ], 500);
        }
    }

    /**
     * Log RFID event with retry logic for database locks
     */
    private function logRfidEvent(string $cardUid, string $action, string $status, string $message, string $deviceId): void
    {
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                RfidLog::create([
                    'card_uid' => $cardUid,
                    'action' => $action,
                    'status' => $status,
                    'message' => $message,
                    'timestamp' => now(),
                    'device_id' => $deviceId,
                ]);
                return; // Success, exit the retry loop
            } catch (\Exception $e) {
                $retryCount++;
                if (strpos($e->getMessage(), 'database is locked') !== false && $retryCount < $maxRetries) {
                    // Wait a bit and retry for database lock errors
                    usleep(100000); // Wait 100ms
                    continue;
                } else {
                    // Log the error but don't fail the main operation
                    Log::warning('Failed to log RFID event after retries: ' . $e->getMessage(), [
                        'card_uid' => $cardUid,
                        'action' => $action,
                        'retry_count' => $retryCount
                    ]);
                    return;
                }
            }
        }
    }

    /**
     * Get current active members
     */
    public function getActiveMembers(): JsonResponse
    {
        $activeMembers = ActiveSession::with(['member:id,first_name,last_name,uid,current_plan_type'])
            ->active()
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->member->id,
                    'name' => $session->member->first_name . ' ' . $session->member->last_name,
                    'uid' => $session->member->uid,
                    'membership_plan' => $session->member->current_plan_type === 'vip' ? 'VIP' : ucfirst($session->member->current_plan_type ?? 'Unknown'),
                    'check_in_time' => $session->check_in_time->format('h:i:s A'),
                    'session_duration' => $session->currentDuration,
                ];
            });

        return response()->json([
            'success' => true,
            'active_members' => $activeMembers,
            'count' => $activeMembers->count(),
        ]);
    }

    /**
     * Get RFID logs
     */
    public function getRfidLogs(Request $request): JsonResponse
    {
        $logs = RfidLog::query()
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->input('action'), function ($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->input('date'), function ($query, $date) {
                return $query->whereDate('timestamp', $date);
            })
            ->orderBy('timestamp', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }

    /**
     * Start the RFID reader process
     */
    public function startRfidReader(Request $request): JsonResponse
    {
        try {
            // Check if RFID reader is already running
            $isRunning = $this->isRfidReaderRunning();
            
            if ($isRunning) {
                return response()->json([
                    'success' => true,
                    'message' => 'RFID reader is already running',
                    'status' => 'running'
                ]);
            }
            
            // Start the RFID reader process
            $this->startRfidProcess();
            
            return response()->json([
                'success' => true,
                'message' => 'RFID reader started successfully',
                'status' => 'running'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start RFID reader: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    
    /**
     * Stop the RFID reader process
     */
    public function stopRfidReader(Request $request): JsonResponse
    {
        try {
            // Stop the RFID reader process
            $this->stopRfidProcess();
            
            return response()->json([
                'success' => true,
                'message' => 'RFID reader stopped successfully',
                'status' => 'stopped'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop RFID reader: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    
    /**
     * Get RFID reader status
     */
    public function getRfidStatus(Request $request): JsonResponse
    {
        try {
            $isRunning = $this->isRfidReaderRunning();
            
            return response()->json([
                'success' => true,
                'rfid_reader_running' => $isRunning,
                'status' => $isRunning ? 'running' : 'stopped',
                'message' => $isRunning ? 'RFID system is operational' : 'RFID reader is not running'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get RFID status: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    
    /**
     * Start RFID system
     */
    public function startRfidSystem(): JsonResponse
    {
        try {
            // Check if RFID reader is already running
            $isRunning = $this->isRfidReaderRunning();
            
            if ($isRunning) {
                return response()->json([
                    'success' => true,
                    'message' => 'RFID reader is already running',
                    'status' => 'running'
                ]);
            }
            
            // Start the RFID reader
            $this->startRfidProcess();
            
            // Wait a moment for initialization
            sleep(2);
            
            // Check if it started successfully
            $isRunning = $this->isRfidReaderRunning();
            
            if ($isRunning) {
                return response()->json([
                    'success' => true,
                    'message' => 'RFID reader started successfully',
                    'status' => 'started'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start RFID reader',
                    'status' => 'failed'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting RFID system: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }
    
    /**
     * Stop RFID system
     */
    public function stopRfidSystem(): JsonResponse
    {
        try {
            // Stop Python processes
            shell_exec('taskkill /F /IM python.exe 2>nul');
            
            // Wait a moment
            sleep(1);
            
            // Check if it stopped successfully
            $processes = shell_exec('tasklist | findstr python');
            
            if (empty($processes)) {
                return response()->json([
                    'success' => true,
                    'message' => 'RFID reader stopped successfully',
                    'status' => 'stopped'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to stop RFID reader',
                    'status' => 'failed'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error stopping RFID system: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }
    
    /**
     * Check if RFID reader process is running
     */
    private function isRfidReaderRunning(): bool
    {
        // Check if Python process is running
        $output = shell_exec('tasklist /FI "IMAGENAME eq python.exe" /FO CSV 2>nul');
        
        if ($output && strpos($output, 'python.exe') !== false) {
            // Check if our specific script is running by looking at command line
            $output = shell_exec('wmic process where "name=\'python.exe\'" get commandline 2>nul');
            
            if ($output && strpos($output, 'rfid_reader.py') !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Start RFID reader process
     */
    private function startRfidProcess(): void
    {
        $pythonPath = 'C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe';
        $scriptPath = base_path('rfid_reader.py');
        
        if (!file_exists($scriptPath)) {
            throw new \Exception('RFID reader script not found at: ' . $scriptPath);
        }
        
        // Start the RFID reader in background using PowerShell
        $command = sprintf(
            'powershell -Command "Start-Process -FilePath \'%s\' -ArgumentList \'%s\' -WindowStyle Hidden"',
            $pythonPath,
            $scriptPath
        );
        
        $result = shell_exec($command);
        
        if ($result === false) {
            throw new \Exception('Failed to start RFID reader process');
        }
        
        // Wait a moment for process to start
        sleep(3);
    }
    
    /**
     * Stop RFID reader process
     */
    private function stopRfidProcess(): void
    {
        // Kill all Python processes running rfid_reader.py
        shell_exec('taskkill /F /IM python.exe /FI "WINDOWTITLE eq rfid_reader.py" >nul 2>&1');
        
        // Also try to kill by process name
        shell_exec('taskkill /F /IM python.exe >nul 2>&1');
    }

    /**
     * Get member suggestions for search
     */
    public function getMemberSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['members' => []]);
        }
        
        $members = Member::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('full_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('member_number', 'like', "%{$query}%");
        })
        ->where('status', 'active')
        ->limit(10)
        ->get()
        ->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'email' => $member->email,
                'membership_plan' => $member->membershipPlan?->name ?? 'No Plan',
                'member_number' => $member->member_number,
            ];
        });
        
        return response()->json(['members' => $members]);
    }
    
}
