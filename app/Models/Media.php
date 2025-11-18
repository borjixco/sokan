<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'file_path', 'title', 'type', 'mime_type', 'size', 'original_name', 'confirmed'
    ];

    public static function uploadFile($file, $title = null)
    {
        $timestamp = now()->timestamp;
        $year = now()->year;
        $month = now()->format('m');
        $extension = $file->getClientOriginalExtension();
        $filename = $timestamp . '_' . uniqid() . '.' . $extension;

        $path = $file->storeAs("media/{$year}/{$month}", $filename, 'public');

        $mediaData = [
            'file_path' => $path,
            'title' => $title,
            'type' => $extension,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'confirmed' => false
        ];

        return self::create($mediaData);
    }

    public function confirmed()
    {
        return $this->update(['confirmed' => true]);
    }


    public function deleteFile()
    {
        Storage::delete('public/' . $this->file_path);
        $this->delete();
    }

}
