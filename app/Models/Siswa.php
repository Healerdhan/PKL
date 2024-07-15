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
}
