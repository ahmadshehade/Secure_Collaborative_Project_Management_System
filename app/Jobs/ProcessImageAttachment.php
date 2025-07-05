<?php

namespace App\Jobs;

use App\Models\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessImageAttachment implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $attachmentId;

    /**
     * Summary of __construct
     * @param int $attachmentId
     */
    public function __construct(int $attachmentId)
    {
        $this->attachmentId = $attachmentId;
    }

    /**
     * Summary of handle
     * @return void
     */
    public function handle(): void
    {
        $attachment = Attachment::find($this->attachmentId);
        if (!$attachment) return;

        if (!str_starts_with($attachment->mime_type, 'image')) return;

        $path = storage_path('app/' . $attachment->disk . '/' . $attachment->path);
        if (!file_exists($path)) {
            logger("Attachment image not found: " . $path);
            return;
        }

        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($path);
            $img->resize(800, null);
            $img->save($path, 80);
            $attachment->update([
                'file_size' => filesize($path),
            ]);
        } catch (\Exception $e) {
            logger("Attachment image processing failed: " . $e->getMessage());
        }
    }
}
