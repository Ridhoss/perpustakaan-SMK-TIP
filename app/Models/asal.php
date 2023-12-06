<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bukus()
    {
        return $this->hasMany(buku::class, 'asl_id', 'id');
    }
}
