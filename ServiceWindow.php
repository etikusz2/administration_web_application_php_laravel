<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ServiceWindowSchedule;

class ServiceWindow extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'administrativeUnitId',
        'windowName',
        'windowLocation',
        'createdByUserId',
        'createDate',
        'updateDate',
        'deleteDate',
    ];


    public function administrativeUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'administrativeUnitId');
    }

    public function schedules()
    {
        return $this->hasMany(ServiceWindowSchedule::class, 'serviceWindowId');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_window_services', 'serviceWindowId', 'serviceId');
    }
}

