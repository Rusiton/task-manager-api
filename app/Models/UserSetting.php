<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'theme'
    ];


    
    public static function boot()
    {
        parent::boot();

        static::updating(function ($setting) {
            if ($setting->isDirty('user_id')) {
                throw new \Exception('Cannot change user id');
            }
        });
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
