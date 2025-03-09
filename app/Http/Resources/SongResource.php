<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'singer' => $this->whenLoaded('singer', function() {
                return [
                    'id' => $this->singer->id,
                    'name' => $this->singer->name,
                    'image' => $this->singer->image ? url('storage/' . $this->singer->image) : null,
                ];
            }),
            'genre' => $this->whenLoaded('genre', function() {
                return [
                    'id' => $this->genre->id,
                    'name' => $this->genre->name,
                ];
            }),
            'cover_image' => $this->cover_image,
            'duration' => $this->duration,
            'duration_formatted' => $this->duration ? gmdate('i:s', $this->duration) : null,
            'file_url' => route('songs.stream', $this->id),
        ];
    }
}
