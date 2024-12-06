<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class LoginApproval extends Model
{
    use Notifiable;
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';
    use SoftDeletes;

    protected $fillable = [
        'loginApplicationId',
        'administrativeUnitId',
        'applicationStatus',
        'createDate',
        'updateDate',
        'deleteDate'
    ];

    public function loginApplication()
    {
        return $this->belongsTo(LoginApplication::class, 'loginApplicationId', 'id');
    }

    public function administrativeUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'administrativeUnitId', 'id');
    }
}
