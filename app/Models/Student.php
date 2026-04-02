<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'students';

    // Jo fields fill ho sakein
    protected $fillable = [
        'student_id',
        'name',
        'college',
        'department',
        'course',
        'year',
        'location',
        'city',
        'state',
        'email',
        'phone',
        'gpa'
    ];

    // Jo fields kabhi bhi return na hon
    protected $hidden = [];

    // Data types define karo
    protected $casts = [
        'gpa' => 'float',
    ];
}