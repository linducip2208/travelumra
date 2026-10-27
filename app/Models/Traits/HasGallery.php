<?php

namespace App\Models\Traits;

use App\Models\Gallery;

trait HasGallery
{
    public function gallery()
    {
        return $this->morphMany(Gallery::class, 'galleriable');
    }

    public function addGallery(string $filePath, ?string $caption = null, ?int $sortOrder = null): Gallery
    {
        return $this->gallery()->create([
            'file_path' => $filePath,
            'caption' => $caption,
            'sort_order' => $sortOrder ?? ($this->gallery()->max('sort_order') ?? 0) + 1,
        ]);
    }
}
