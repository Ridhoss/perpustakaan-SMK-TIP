<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detailpinjam extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pinjams()
    {
        return $this->belongsTo(pinjam::class, 'kode', 'kode');
    }
}
