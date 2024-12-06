<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class WebApplicationService extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['webApplicationId', 'webServiceName'];

    public function webApplication()
    {
        return $this->belongsTo(WebApplication::class, 'webApplicationId');
    }
}

