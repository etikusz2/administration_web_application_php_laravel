<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAdministrativeUnit()
    {
        return $this->role === 'administrative_unit';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function loginApplications()
    {
        return $this->hasOne(LoginApplication::class, 'userId');
    }

    public function getAdministrativeUnits()
    {
        $units = [];
        if (!isset($this->loginApprovals)) {
            $this->load('loginApprovals.administrativeUnit');
        }

        foreach ($this->loginApprovals as $approval) {
            if ($unit = $approval->administrativeUnit) {
                $units[$unit->id] = $unit;
            }
        }

        return array_values($units);
    }

    public function getDepartmentsForUnit($unitId)
    {
        return AdministrativeUnitDepartment::where('administrativeUnitId', $unitId)->get();
    }

    public function loginApprovals()
    {
        return $this->hasManyThrough(
            LoginApproval::class,
            LoginApplication::class,
            'userId',    // Foreign key on LoginApplication table
            'loginApplicationId',  // Foreign key on LoginApproval table
            'id',         // Local key on User table
            'id'          // Local key on LoginApplication table
        );
    }

    public function appointment()
    {
        return $this->hasMany(Appointment::class, 'appointmentUserId');
    }

    public function serviceRequest()
    {
        return $this->hasMany(ServiceRequest::class, 'requestByUserId');
    }

    /**
     * Get the notifications for the user.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(DatabaseNotification::class);
    }

    /**
     * Define a relationship method for unread notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
}
