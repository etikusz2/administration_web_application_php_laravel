<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginApplication extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';
    use SoftDeletes;

    protected $fillable = [
        'userId',
        'firstName',
        'lastName',
        'phoneNumber',
        'idType',
        'idSerial',
        'idNumber',
        'personalIdentificationNumber',
        'county',
        'city',
        'street',
        'streetNumber',
        'block',
        'blockEntrance',
        'apartmentNumber',
        'idImageURL',
        'selfieImageURL',
        'createDate',
        'updateDate',
        'deleteDate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function loginApprovals()
    {
        return $this->hasMany(LoginApproval::class, 'loginApplicationId', 'id');
    }
}

