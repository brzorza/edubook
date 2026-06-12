<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'imie',
        'nazwisko',
        'email',
        'password',
        'rola',
        'czy_pierwsze_logowanie',
        'last_activity',
        'school_class_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'czy_pierwsze_logowanie' => 'boolean',
        'last_activity' => 'datetime',
    ];

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'user_id', 'subject_id');
    }

    public function grades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }
}