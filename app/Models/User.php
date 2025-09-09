<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasToken;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'profile', 'settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }



    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }



    public function owned_boards(): HasMany
    {
        return $this->hasMany(Board::class, 'owner_id');
    }



    public function joined_boards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class)->withPivot('role');
    }

    

    public function assigned_tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }


    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }



    public function recievedInvitations(): HasMany
    {
        return $this->hasMany(BoardUserInvitation::class);
    }



    public function sentInvitations(): HasMany
    {
        return $this->hasMany(BoardUserInvitation::class, 'invited_by');
    }
}
