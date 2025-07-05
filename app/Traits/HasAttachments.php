<?php

namespace App\Traits;

use App\Jobs\ProcessImageAttachment;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAttachments
{

    /**
     * Summary of uploadAttachments
     * @param array $files
     * @param mixed $attachable_id
     * @param mixed $attachable_type
     * @param string $disk
     * @return void
     */
    public function uploadAttachments(array $files, $attachable_id, $attachable_type, string $disk = 'private'): void
    {
        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random() . '.' . $extension;
            $path = "attachments/" . class_basename($attachable_type) . '/' . $attachable_id . '/' . $filename;

            $file->storeAs("attachments/" . class_basename($attachable_type) . '/' . $attachable_id, $filename, $disk);

            $fileSize = Storage::disk($disk)->size($path);

            $att = Attachment::create([
                'path' => $path,
                'disk' => $disk,
                'file_name' => $filename,
                'file_size' => $fileSize,
                'mime_type' => $file->getClientMimeType(),
                'attachable_id' => $attachable_id,
                'attachable_type' => $attachable_type,
            ]);

           
            dispatch(new ProcessImageAttachment($att->id));
        }
    }


    /**
     * Summary of deleteAttachments
     * @param mixed $attachable
     * @return void
     */
    public function deleteAttachments($attachable): void
    {

        if (!method_exists($attachable, 'attachments')) {
            return;
        }

        foreach ($attachable->attachments as $attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);
            $attachment->delete();
        }
    }
}
