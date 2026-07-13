<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;  // ✅ បន្ថែមបន្ទាត់នេះ

class Attendance extends Model
{
    public $timestamps = false;

    protected $table      = 'attendance';
    protected $primaryKey = 'att_id';

    protected $fillable = [
        'emp_id',
        'date',
        'time_in',
        'time_out',
        'gps_location',
        'device_id',
        'scan_token',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}