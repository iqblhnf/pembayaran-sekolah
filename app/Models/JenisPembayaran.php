<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    protected $fillable = [
        'nama_pembayaran'
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'jenis_pembayaran_id');
    }
}
