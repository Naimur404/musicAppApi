<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request) :array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'songs_count' => $this->whenLoaded('songs', function() {
                return $this->songs->count();
            }),
        ];
    }
}
