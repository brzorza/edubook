<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name', 'description'];

    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'user_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'subject_id');
    }
}