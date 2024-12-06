<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class WebApplication extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['webApplicationName', 'webApplicationType'];

    public function webApplicationServices()
    {
        return $this->hasMany(WebApplicationService::class, 'webApplicationId');
    }
}
