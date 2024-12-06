<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceWindowSchedule extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serviceWindowId',
        'startDateTime',
        'endDateTime',
        'createdByUserId',
        'createDate',
        'updateDate',
        'deleteDate',
    ];

    public function serviceWindow()
    {
        return $this->belongsTo(ServiceWindow::class, 'serviceWindowId');
    }
}
