<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Singer extends Model
{
    protected $fillable = ['name', 'image', 'bio'];
    
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
