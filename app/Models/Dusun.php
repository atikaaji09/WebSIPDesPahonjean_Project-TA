<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Rtrw;

class Dusun extends Model
{
    protected $table = 'dusun';

    protected $fillable = [
        'nama_dusun'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rtrw()
    {
        return $this->hasMany(Rtrw::class);
    }
}
