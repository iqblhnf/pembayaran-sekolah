<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'kelas_id',
        'nis',
        'nama',
        'alamat',
        'nama_ortu',
        'telp_ortu',
        'status',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
