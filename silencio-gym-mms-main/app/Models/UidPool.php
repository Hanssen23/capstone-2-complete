<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UidPool extends Model
{
    protected $table = 'uid_pool';
    
    protected $fillable = [
        'uid',
        'status',
        'assigned_at',
        'returned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get an available UID from the pool
     */
    public static function getAvailableUid(): ?string
    {
        // Use database transaction with locking to prevent race conditions
        return \DB::transaction(function () {
            // Lock the row for update to prevent concurrent access
            $uidPool = self::where('status', 'available')
                ->lockForUpdate()
                ->first();

            if ($uidPool) {
                $uidPool->update([
                    'status' => 'assigned',
                    'assigned_at' => now(),
                ]);

                \Log::info('UID assigned from pool', [
                    'uid' => $uidPool->uid,
                    'assigned_at' => now()
                ]);

                return $uidPool->uid;
            }

            // If no UIDs available, try to generate some new ones
            \Log::info('No UIDs available, generating new ones');
            self::generateNewUids();

            // Try again after generating new UIDs
            $uidPool = self::where('status', 'available')
                ->lockForUpdate()
                ->first();

            if ($uidPool) {
                $uidPool->update([
                    'status' => 'assigned',
                    'assigned_at' => now(),
                ]);

                \Log::info('UID assigned from newly generated pool', [
                    'uid' => $uidPool->uid,
                    'assigned_at' => now()
                ]);

                return $uidPool->uid;
            }

            \Log::error('Failed to get available UID even after generating new ones');
            return null;
        });
    }

    /**
     * Generate new UIDs when pool is empty
     */
    public static function generateNewUids(int $count = 50): void
    {
        $newUids = [];
        
        for ($i = 0; $i < $count; $i++) {
            // Generate a random 8-character hex UID
            $uid = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            
            // Ensure it's unique in both UID pool and members table
            while (self::where('uid', $uid)->exists() || 
                   \App\Models\Member::where('uid', $uid)->exists()) {
                $uid = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            }
            
            $newUids[] = $uid;
        }
        
        // Insert new UIDs in batch for better performance
        $uidData = [];
        foreach ($newUids as $uid) {
            $uidData[] = [
                'uid' => $uid,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        self::insert($uidData);
    }

    /**
     * Return a UID back to the pool
     */
    public static function returnUid(string $uid): bool
    {
        $uidPool = self::where('uid', $uid)->first();

        if ($uidPool) {
            $uidPool->update([
                'status' => 'available',
                'returned_at' => now(),
            ]);

            \Log::info('UID returned to pool', [
                'uid' => $uid,
                'returned_at' => now()
            ]);

            return true;
        }

        return false;
    }

    /**
     * Reset a specific UID to available status
     */
    public static function resetUid(string $uid): bool
    {
        $uidPool = self::where('uid', $uid)->first();

        if ($uidPool) {
            $uidPool->update([
                'status' => 'available',
                'assigned_at' => null,
                'returned_at' => now(),
            ]);

            \Log::info('UID reset to available', [
                'uid' => $uid,
                'reset_at' => now()
            ]);

            return true;
        }

        return false;
    }



    /**
     * Check if a UID is available
     */
    public static function isUidAvailable(string $uid): bool
    {
        return self::where('uid', $uid)
                   ->where('status', 'available')
                   ->exists();
    }

    /**
     * Get all available UIDs
     */
    public static function getAvailableUids(): array
    {
        return self::where('status', 'available')
                   ->pluck('uid')
                   ->toArray();
    }

    /**
     * Get all assigned UIDs
     */
    public static function getAssignedUids(): array
    {
        return self::where('status', 'assigned')
                   ->pluck('uid')
                   ->toArray();
    }

    /**
     * Scope for available UIDs
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for assigned UIDs
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }
}