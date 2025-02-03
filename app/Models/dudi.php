<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class dudi extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'dudi_siswa', 'dudi_id', 'siswa_id')
            ->withTimestamps();
    }

    public function pembimbings()
    {
        return $this->belongsToMany(Pembimbing::class, 'dudi_pembimbing', 'dudi_id', 'pembimbing_id');
    }

    public function calculateDistance($latitude, $longitude)
    {
        $earthRadius = 6371; // Radius bumi dalam KM

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class);
    }
}
