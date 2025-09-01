<?php

namespace App\Models;

use App\Models\Interfaces\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model implements Commentable
{
    protected $fillable = [
        'column_id',
        'assigned_to',
        'name',
        'description',
        'position',
        'due_date',
    ];



    public function getBoard(): ?Board
    {
        return $this->column->board;
    }



    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }



    public function assigned_to(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}