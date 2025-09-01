<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Column extends Model
{
    protected $fillable = [
        'name',
        'position',
    ];



    protected static function boot()
    {
        parent::boot();

        static::updating(function ($column) {
            if ($column->isDirty('board_id')) {
                throw new \Exception('Cannot move column to different board');
            }
        }) ;
    }


    
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }



    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
