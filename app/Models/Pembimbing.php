<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembimbing extends Model
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

    public function dudi1()
    {
        return $this->belongsTo(dudi::class, 'dudi_id1');
    }

    public function dudi2()
    {
        return $this->belongsTo(dudi::class, 'dudi_id2');
    }

    public function dudi3()
    {
        return $this->belongsTo(dudi::class, 'dudi_id3');
    }

    public function dudi4()
    {
        return $this->belongsTo(dudi::class, 'dudi_id4');
    }

    public function dudi5()
    {
        return $this->belongsTo(dudi::class, 'dudi_id5');
    }
}
