<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    public function index()
    {
        $songs = Song::with(['singer', 'genre'])->get();
        return SongResource::collection($songs);
    }
    
    public function show($id)
    {
        $song = Song::with(['singer', 'genre'])->findOrFail($id);
        return new SongResource($song);
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $songs = Song::where('title', 'LIKE', "%{$query}%")
            ->orWhereHas('singer', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('genre', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->with(['singer', 'genre'])
            ->get();
            
        return SongResource::collection($songs);
    }
    
    public function stream($id)
    {
        $song = Song::findOrFail($id);
        $filePath = storage_path('app/' . $song->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        // Determine MIME type based on file extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeType = 'audio/mpeg'; // Default
        
        // Set correct MIME type based on file extension
        if ($extension) {
            $mimeTypes = [
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
                'flac' => 'audio/flac',
                'm4a' => 'audio/mp4',
            ];
            
            if (isset($mimeTypes[strtolower($extension)])) {
                $mimeType = $mimeTypes[strtolower($extension)];
            }
        }
        
        $fileSize = filesize($filePath);
        $fileTime = date('r', filemtime($filePath));
        
        $fm = @fopen($filePath, 'rb');
        if (!$fm) {
            return response()->json(['error' => 'Cannot open file'], 500);
        }
        
        $begin = 0;
        $end = $fileSize - 1;
        
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Last-Modified' => $fileTime,
            'Accept-Ranges' => 'bytes',
            // Add CORS headers if needed
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD',
            'Access-Control-Allow-Headers' => 'Content-Type, Range',
            'Access-Control-Expose-Headers' => 'Content-Range, Content-Length, Content-Type'
        ];
        
        if (isset($_SERVER['HTTP_RANGE'])) {
            $ranges = explode('=', $_SERVER['HTTP_RANGE'], 2);
            
            if (count($ranges) == 2 && $ranges[0] === 'bytes') {
                $ranges = explode('-', $ranges[1]);
                $begin = intval($ranges[0]);
                
                if (count($ranges) > 1 && !empty($ranges[1])) {
                    $end = intval($ranges[1]);
                }
                
                $headers['Content-Length'] = $end - $begin + 1;
                $headers['Content-Range'] = "bytes $begin-$end/$fileSize";
                
                return response()->stream(function() use ($fm, $begin, $end) {
                    $buffer = 1024 * 8;
                    $bytes = $begin;
                    
                    fseek($fm, $begin);
                    
                    while(!feof($fm) && $bytes <= $end) {
                        $bytesToRead = min($buffer, $end - $bytes + 1);
                        echo fread($fm, $bytesToRead);
                        $bytes += $bytesToRead;
                        flush();
                    }
                    
                    fclose($fm);
                }, 206, $headers); // Use 206 for partial content
            }
        }
        
        // For non-range requests, return full file
        return response()->stream(function() use ($fm, $fileSize) {
            $buffer = 1024 * 8;
            $totalBytes = 0;
            
            while(!feof($fm) && $totalBytes < $fileSize) {
                $bytesToRead = min($buffer, $fileSize - $totalBytes);
                echo fread($fm, $bytesToRead);
                $totalBytes += $bytesToRead;
                flush();
            }
            
            fclose($fm);
        }, 200, $headers);
    }
}
