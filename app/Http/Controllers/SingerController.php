<?php

namespace App\Http\Controllers;

use App\Http\Resources\SingerResource;
use App\Models\Singer;
use Illuminate\Http\Request;

class SingerController extends Controller
{
    public function index()
    {
        $singers = Singer::all();
        return SingerResource::collection($singers);
    }
    
    public function show($id)
    {
        $singer = Singer::findOrFail($id);
        return new SingerResource($singer);
    }
    
    public function songs($id)
    {
        $singer = Singer::findOrFail($id);
        return response()->json([
            'singer' => new SingerResource($singer),
            'songs' => $singer->songs()->with('genre')->get()
        ]);
    }
}
