<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'value',
        'weight',
        'type_description',
        'lesson_id',
        'student_id',
        'teacher_id'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    protected static function booted()
    {
        static::created(function ($grade) {
            \App\Models\SystemLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'UTWORZENIE_OCENY',
                'model_type' => Grade::class,
                'model_id' => $grade->id,
                'description' => "Wystawiono ocenę {$grade->value} (waga {$grade->weight}) dla studenta o ID {$grade->student_id}.",
                'ip_address' => request()->ip()
            ]);
        });

        static::updated(function ($grade) {
            \App\Models\SystemLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'EDYCJA_OCENY',
                'model_type' => Grade::class,
                'model_id' => $grade->id,
                'description' => "Zmieniono ocenę na {$grade->value} (waga {$grade->weight}) dla studenta o ID {$grade->student_id}.",
                'ip_address' => request()->ip()
            ]);
        });

        static::deleted(function ($grade) {
            \App\Models\SystemLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'USUNIĘCIE_OCENY',
                'model_type' => Grade::class,
                'model_id' => $grade->id,
                'description' => "Usunięto ocenę o wartości {$grade->value} należącą do studenta o ID {$grade->student_id}.",
                'ip_address' => request()->ip()
            ]);
        });
    }
}