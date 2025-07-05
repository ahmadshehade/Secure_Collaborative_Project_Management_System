<?php

namespace Tests\Unit;

use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentObserverTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Summary of test_attachment_observer_deletes_file_and_empty_directory_on_delete
     * @return void
     */
    public function test_attachment_observer_deletes_file_and_empty_directory_on_delete()
    {
        Storage::fake('private');


        $path = 'attachments/test/file.txt';
        Storage::disk('private')->put($path, 'Dummy content');


        Storage::disk('private')->assertExists($path);


        $attachment = Attachment::factory()->create([
            'disk' => 'private',
            'path' => $path,
        ]);


        $attachment->delete();


        Storage::disk('private')->assertMissing($path);


        $this->assertEmpty(Storage::disk('private')->allFiles(dirname($path)));
    }
}
