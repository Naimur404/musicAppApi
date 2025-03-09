<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        return GenreResource::collection($genres);
    }
    
    public function show($id)
    {
        $genre = Genre::findOrFail($id);
        return new GenreResource($genre);
    }
    
    public function songs($id)
    {
        $genre = Genre::findOrFail($id);
        return response()->json([
            'genre' => new GenreResource($genre),
            'songs' => $genre->songs()->with('singer')->get()
        ]);
    }
}
