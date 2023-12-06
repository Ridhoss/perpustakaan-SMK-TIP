<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function kategoris()
    {
        return $this->belongsTo(kategori::class, 'ktg_id', 'id');
    }

    public function bahasas()
    {
        return $this->belongsTo(bahasa::class, 'bhs_id', 'id');
    }

    public function asals()
    {
        return $this->belongsTo(asal::class, 'asl_id', 'id');
    }
}
