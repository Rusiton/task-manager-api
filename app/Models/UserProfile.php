<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];



    public static function boot()
    {
        parent::boot();

        static::updating(function ($profile) {
            if ($profile->isDirty('user_id')) {
                throw new \Exception('Cannot change user id');
            }
        });
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
