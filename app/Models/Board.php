<?php

namespace App\Models;

use App\Traits\HasToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasToken;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];


    
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'owner_id');
    }



    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }



    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }



    public function invitations(): HasMany
    {
        return $this->hasMany(BoardUserInvitation::class);
    }



    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }
}
