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
        $uidPool = self::where('status', 'available')->first();
        
        if ($uidPool) {
            $uidPool->update([
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);
            
            return $uidPool->uid;
        }
        
        return null;
    }

    /**
     * Return a UID to the pool
     */
    public static function returnUid(string $uid): bool
    {
        $uidPool = self::where('uid', $uid)->first();
        
        if ($uidPool) {
            $uidPool->update([
                'status' => 'available',
                'returned_at' => now(),
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