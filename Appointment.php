<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $fillable = [
        'serviceId',
        'appointmentDate',
        'appointmentStartTime',
        'appointmentEndTime',
        'appointmentUserId',
        'appointmentStatus',
    ];



    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'appointmentUserId');
    }
}
