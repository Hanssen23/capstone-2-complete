<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_uid',
        'action',
        'status',
        'message',
        'timestamp',
        'device_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Scope for successful events
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed events
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for today's events
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }

    /**
     * Scope for unknown card events
     */
    public function scopeUnknownCards($query)
    {
        return $query->where('action', 'unknown_card');
    }
}
