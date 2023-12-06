<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pinjam extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function detailpinjams()
    {
        return $this->hasMany(detailpinjam::class, 'kode', 'kode');
    }
}
