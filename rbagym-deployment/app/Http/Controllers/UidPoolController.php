<?php

namespace App\Http\Controllers;

use App\Models\UidPool;
use Illuminate\Http\Request;

class UidPoolController extends Controller
{
    /**
     * Display the UID pool management page
     */
    public function index()
    {
        $availableUids = UidPool::available()->orderBy('created_at')->get();
        $assignedUids = UidPool::assigned()->orderBy('assigned_at', 'desc')->get();
        
        $availableCount = $availableUids->count();
        $assignedCount = $assignedUids->count();
        $totalCount = $availableCount + $assignedCount;

        return view('uid-pool.index', compact(
            'availableUids',
            'assignedUids',
            'availableCount',
            'assignedCount',
            'totalCount'
        ));
    }

    /**
     * Refresh the UID pool (return all UIDs to available status)
     */
    public function refresh()
    {
        try {
            // Reset all UIDs to available status
            UidPool::where('status', 'assigned')->update([
                'status' => 'available',
                'returned_at' => now(),
                'assigned_at' => null,
            ]);

            return redirect()->route('uid-pool.index')
                ->with('success', 'UID pool refreshed successfully. All UIDs are now available.');
                
        } catch (\Exception $e) {
            return redirect()->route('uid-pool.index')
                ->with('error', 'Failed to refresh UID pool: ' . $e->getMessage());
        }
    }

    /**
     * Get UID pool status as JSON (for API calls)
     */
    public function status()
    {
        $availableCount = UidPool::available()->count();
        $assignedCount = UidPool::assigned()->count();
        $totalCount = $availableCount + $assignedCount;

        return response()->json([
            'available' => $availableCount,
            'assigned' => $assignedCount,
            'total' => $totalCount,
            'available_uids' => UidPool::available()->pluck('uid')->toArray(),
            'assigned_uids' => UidPool::assigned()->pluck('uid')->toArray(),
        ]);
    }
}