<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageUploader
{
    private const ALLOWED = ['jpg', 'jpeg', 'png', 'webp'];

    public function upload(UploadedFile $file, string $directory): string
    {
        // The extension comes from the file content, not from the client's file name.
        $extension = strtolower((string) $file->extension());

        abort_unless(in_array($extension, self::ALLOWED, true), 422);

        $folder = public_path('uploads/' . $directory);
        File::ensureDirectoryExists($folder, 0755);

        $name = Str::uuid() . '.' . $extension;
        $file->move($folder, $name);

        return "uploads/{$directory}/{$name}";
    }

    public function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
