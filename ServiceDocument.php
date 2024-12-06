<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDocument extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serviceId',
        'documentName',
        'documentDescription',
        'documentURL',
        'createdForUserId',
        'createdByUserId'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceId');
    }

    public function createdForUser()
    {
        return $this->belongsTo(User::class, 'createdForUserId');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'createdByUserId');
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'requestId');
    }
}
