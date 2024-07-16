<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class dudi extends Model
{
    use HasFactory, HasUuids;

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

    public function siswa1()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id1');
    }

    public function siswa2()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id2');
    }

    public function siswa3()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id3');
    }

    public function siswa4()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id4');
    }

    public function siswa5()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id5');
    }

    public function siswa6()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id6');
    }

    public function siswa7()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id7');
    }

    public function siswa8()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id8');
    }

    public function siswa9()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id9');
    }

    public function siswa10()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id10');
    }

    public function siswa11()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id11');
    }

    public function siswa12()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id12');
    }

    public function siswa13()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id13');
    }

    public function siswa14()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id14');
    }
}
