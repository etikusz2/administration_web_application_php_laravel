<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdministrativeUnitDepartment extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['administrativeUnitId', 'departmentName', 'webApplicationId', 'webApplicationURL'];

    public function administrativeUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'administrativeUnitId');
    }

    public function webApplication()
    {
        return $this->belongsTo(WebApplication::class, 'webApplicationId');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'departmentId');
    }
}
