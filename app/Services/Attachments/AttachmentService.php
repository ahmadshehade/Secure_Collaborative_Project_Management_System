<?php 

namespace App\Services\Attachments;

use App\Interfaces\Repositories\Attachments\AttachmentRepositoryInterface;
use App\Interfaces\Services\Attachments\AttachmentInterface;
use App\Models\Attachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttachmentService implements AttachmentInterface
{
    protected $repo;
    use AuthorizesRequests;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Repositories\Attachments\AttachmentRepositoryInterface $repo
     */
    public function __construct(AttachmentRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Summary of index
     * @return array{data: mixed, message: string, success: bool}
     */
    public function index()
    {
         $user = auth('api')->user();

 
     $this->authorize('viewAny',Attachment::class);

     if($user->hasRole('admin')||$user->hasRole('project_manager')){
        $attachments=$this->repo->all();
     }
     else {
          
          $attachments = Attachment::visibleToUser($user)->get();
     }
    return [
        'message' => 'Successfully fetched attachments',
        'success' => true,
        'data' => $attachments,
    ];
    }

    /**
     * Summary of show
     * @param int $id
     */
    public function show(int $id)
    {
        $attachment=$this->repo->find($id);
        $this->authorize('view',$attachment);
        return $attachment;
    }
}
 

  
