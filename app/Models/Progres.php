<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progres extends Model
{
    use HasFactory;
    
    protected $table = 'progres';

    protected $fillable = [
        'user_id',
        'target_id',
        'setoran',
        'tanggal_setoran',
        'waktu_setoran'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->hasMany(Target::class);
    }
}