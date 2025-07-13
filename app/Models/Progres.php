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

    protected $casts = [
        'tanggal_setoran' => 'date',
        'waktu_setoran' => 'datetime:H:i',
    ];

    public function getTanggalSetoranFormattedAttribute()
    {
        return $this->tanggal_setoran ? $this->tanggal_setoran->format('d-m-Y') : null;
    }

    public function getWaktuSetoranFormattedAttribute()
    {
        return $this->waktu_setoran ? \Carbon\Carbon::parse($this->waktu_setoran)->format('H:i') : null;
    }


    public function progres()
    {
        return $this->hasMany(Progres::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }
}
