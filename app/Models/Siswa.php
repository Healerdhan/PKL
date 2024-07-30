<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Siswa extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nama_siswa',
        'jenis_kelamin',
        'NISN',
        'tempat_lahir',
        'tanggal_lahir',
        'category_id',
        'alamat',
        'latitude',
        'longitude',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
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
}
