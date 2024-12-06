<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


class AdministrativeUnit extends Model
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected $fillable = ['name'];

    public function loginApprovals()
    {
        return $this->hasMany(LoginApproval::class, 'administartiveUnitId');
    }

    public function departments()
    {
        return $this->hasMany(AdministrativeUnitDepartment::class, 'administrativeUnitId');
    }

    public function serviceWindows()
    {
        return $this->hasMany(ServiceWindow::class, 'administrativeUnitId');
    }

    public function getDepartments($unitId)
    {
        $departments = AdministrativeUnitDepartment::where('administrativeUnitId', $unitId)->get();
        return response()->json($departments);
    }

    /**
     * Get the users associated with the administrative unit.
     *
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'notifiable');
    }
}
