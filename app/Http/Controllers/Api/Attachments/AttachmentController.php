<?php

namespace App\Http\Controllers\Api\Attachments;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Attachments\AttachmentInterface;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected $attachments;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Attachments\AttachmentInterface $attachment
     */
    public function __construct(AttachmentInterface $attachment)
    {
        $this->attachments = $attachment;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index()
    {
        $data = $this->attachments->index();
        return response()->json(['data' => $data], 200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->attachments->show($id);
        return response()->json(['data' =>
        $data], 200);
    }
}
