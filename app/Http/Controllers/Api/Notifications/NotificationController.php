<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Notifications\NotificationInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Summary of notifications
     * @var 
     */
    protected $notifications;
    /**
     * Summary of __construct
     * @param \App\Interfaces\Services\Notifications\NotificationInterface $notifications
     */
    public function __construct(NotificationInterface $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @param mixed $limit
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function  index(Request $request, $limit)
    {
        $data = $this->notifications->index($request->user(), $limit);
        return response()->json([
            'result' => $data,
        ], 200);
    }

    /**
     * Summary of unread
     * @param \Illuminate\Http\Request $request
     * @param mixed $limit
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function unread(Request $request, $limit)
    {
        $data = $this->notifications->unread($request->user(), $limit);
        return response()->json([
            'result' => $data
        ], 200);
    }

    /**
     * Summary of markAsRead
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, string $id)
    {
        $data = $this->notifications->markAsRead($request->user(), $id);
        return response()->json([
            'result' => $data
        ], 200);
    }

    /**
     * Summary of destroy
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $id)
    {
        $data = $this->notifications->destroy($request->user(), $id);
        return response()->json([
            'result' => $data
        ], 200);
    }

    /**
     * Summary of deleteAllRead
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deleteAllRead(Request $request)
    {
        $user = $request->user();
        $result = $this->notifications->deleteAllRead($user);

        return response()->json(['result' => $result], 200);
    }

    /**
     * Summary of allMarkSaRead
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function allMarkSaRead(Request $request)
    {
        $user = $request->user();
        $result = $this->notifications->allMarkSaRead($user);
        return response()->json(['result' => $result], 200);
    }
}
