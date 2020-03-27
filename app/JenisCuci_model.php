<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisCuci_model extends Model
{
    protected $table="jenis_cuci";
    protected $primarykey="id";
    public $timestamps=false;
    protected $fillable = [
        'nama_jenis', 'harga_per_kilo',
    ];
}
