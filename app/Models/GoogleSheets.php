<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GSheetsAPI extends Model
{
    use HasFactory;

    protected $table = 'gsheet_apis';

    protected $fillable = [
        'nama',
        'tgl_lahir',
        'alamat',
        'no_telp',
    ];
}
