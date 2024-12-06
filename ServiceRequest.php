<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ServiceRequest extends Model
{
    use Notifiable;
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requestByUserId',
        'serviceId',
        'requestDate',
        'requestStatus',
        'requestComments',
        'createdByUserId',
        'createDate',
        'updateDate',
        'deleteDate',
    ];

    protected $dates = ['requestDate', 'createDate', 'updateDate', 'deleteDate'];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requestByUserId');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceId');
    }
}
