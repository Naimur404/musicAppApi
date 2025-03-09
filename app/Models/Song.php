<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = ['title', 'singer_id', 'genre_id', 'file_path', 'cover_image', 'duration'];
    protected $appends = ['file_url'];
    
    public function singer()
    {
        return $this->belongsTo(Singer::class);
    }
    
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function getFileUrlAttribute()
{
    return route('songs.stream', $this->id);
}
}
