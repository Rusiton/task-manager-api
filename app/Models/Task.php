<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'column_id',
        'assigned_to',
        'name',
        'description',
        'position',
        'due_date',
    ];



    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }



    public function assigned_to(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}