<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $table = 'recomendations';

    protected $fillable = [
        'title',
        'ticket',
        'food',
        'transport',
        'others',
        'image_path',
        'location_name',
        'latitude',
        'longitude',
        'description'
    ];

    protected $casts = [
        'ticket' => 'float',
        'food' => 'float',
        'transport' => 'float',
        'others' => 'float',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    protected $appends = ['total_estimated'];

    public function getTotalEstimatedAttribute()
    {
        return ($this->ticket ?? 0) + ($this->food ?? 0) + ($this->transport ?? 0) + ($this->others ?? 0);
    }
}
