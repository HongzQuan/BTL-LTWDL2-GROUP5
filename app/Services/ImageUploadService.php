<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    /**
     * Lưu file ảnh vào storage
     */
    public function upload(UploadedFile $file, string $folder = 'images'): string
    {
        // Store tự động lưu vào storage/app/public/{$folder} và sinh tên file ngẫu nhiên
        return $file->store($folder, 'public');
    }

    /**
     * Xóa file ảnh khỏi storage
     */
    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}