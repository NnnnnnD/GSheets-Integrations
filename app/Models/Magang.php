<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nim',
        'sekolah_universitas',
        'jurusan_prodi',
        'mulai_magang',
        'selesai_magang',
        'surat_rujukan',
        'surat_permohonan_magang',
    ];
}

