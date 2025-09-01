<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];


    
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }



    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }



    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }
}
