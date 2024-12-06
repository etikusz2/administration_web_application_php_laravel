<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnounceCategory extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'administrativeUnitId',
        'announceCategoryName'
    ];

    public function administrativeUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'administrativeUnitId');
    }

    public function announces()
    {
        return $this->hasMany(Announce::class);
    }
}
