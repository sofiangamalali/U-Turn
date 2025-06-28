<?php
namespace App\Traits;

use App\Models\Voice;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasVoices
{
    public function uploadVoice(UploadedFile $file, ?string $folder = null): void
    {
        $this->storeSingleVoice($file, $folder);
    }

    protected function storeSingleVoice(UploadedFile $file, ?string $folder = null): void
    {
        $folder = $folder ?? $this->getVoiceUploadFolder();

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '-' . uniqid() . '.' . $extension;

        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0755, true);
        }

        $file->move(public_path($folder), $fileName);

        $this->voices()->create([
            'file_path' => $fileName,
        ]);
    }

    protected function getVoiceUploadFolder(): string
    {
        return 'voices';
    }

    public function voices()
    {
        return $this->morphMany(Voice::class, 'voiceable');
    }
}
