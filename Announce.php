<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announce extends Model
{
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'updateDate';
    const DELETED_AT = 'deleteDate';

    use HasFactory, SoftDeletes;

    protected $dates = ['createdDate'];
    protected $fillable = [
        'announcesCategoryId',
        'announceText',
        'announceImageURL',
        'createdByUserId'
    ];

    public function category()
    {
        return $this->belongsTo(AnnounceCategory::class, 'announcesCategoryId');
    }
}
