<?php

namespace App\Models;

use App\Traits\HasToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardUserInvitation extends Model
{
    use HasToken;

    protected $fillable = [
        'board_id',
        'user_id',
        'invited_by',
        'token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];



    public static function findByToken($token)
    {
        return static::where('token', $token)->first();
    }



    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }



    public function isExpired(): Bool
    {
        return $this->expires_at < now();
    }



    public function isPending(): Bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}
