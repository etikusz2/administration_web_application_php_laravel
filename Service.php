<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'departmentId',
        'serviceName',
        'serviceDescription'
    ];

    public function department()
    {
        return $this->belongsTo(AdministrativeUnitDepartment::class, 'departmentId');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function serviceDocuments()
    {
        return $this->hasMany(ServiceDocument::class);
    }

    public function serviceWindowServices()
    {
        return $this->belongsTo(ServiceWindowService::class, 'serviceId');
    }

    public function serviceWindows()
    {
        return $this->belongsToMany(ServiceWindow::class, 'service_window_services', 'serviceId', 'serviceWindowId');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
