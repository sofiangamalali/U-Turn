<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasImages
{

    public function uploadImage(UploadedFile $file, ?string $folder = null): void
    {
        $this->storeSingleImage($file, $folder);
    }


    public function uploadImages(array $files, ?string $folder = null): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $this->storeSingleImage($file, $folder);
            }
        }
    }


    protected function storeSingleImage(UploadedFile $file, ?string $folder = null): void
    {
        $folder = $folder ?? $this->getUploadFolder();

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '-' . uniqid() . '.' . $extension;


        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0755, true);
        }

        $file->move(public_path($folder), $fileName);

        $this->images()->create([
            'file_path' => $fileName,
        ]);
    }


    protected function getUploadFolder(): string
    {
        return Str::plural(Str::lower(class_basename($this)));
    }


    public function deleteImages(?string $folder = null): void
    {
        $folder = $folder ?? $this->getUploadFolder();

        foreach ($this->images as $image) {
            $path = public_path($folder . '/' . $image->file_path);

            if (file_exists($path)) {
                @unlink($path);
            }

            $image->delete();
        }
    }

    public function updateImages(array $files, ?string $folder = null): void
    {
        $this->deleteImages($folder);
        $this->uploadImages($files, $folder);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
