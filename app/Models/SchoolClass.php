<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'school_year',
    ];

    public function students()
    {
        return $this->hasMany(User::class, 'school_class_id')->where('rola', 'uczen');
    }
}