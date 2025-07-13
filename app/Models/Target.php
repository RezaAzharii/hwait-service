<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $table = 'target';

    protected $fillable = [
        'user_id',
        'title',
        'ticket',
        'food',
        'transport',
        'others',
        'image_path',
        'location_name',
        'latitude',
        'longitude',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progres()
    {
        return $this->hasMany(Progres::class);
    }
}
