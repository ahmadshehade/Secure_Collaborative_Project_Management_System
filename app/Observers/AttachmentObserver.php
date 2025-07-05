<?php

namespace App\Observers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentObserver
{

    /**
     * Handle the Attachment "deleted" event.
     */
    public function deleted(Attachment $attachment): void
    {
        $disk = $attachment->disk;
        $path = $attachment->path;
        $directory = dirname($path);


        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }


        if (empty(Storage::disk($disk)->allFiles($directory))) {
            Storage::disk($disk)->deleteDirectory($directory);
        }
    }
}
