<?php 

namespace App\Repositories\Attachments;

use App\Interfaces\Repositories\Attachments\AttachmentRepositoryInterface;
use App\Models\Attachment;


class AttachmentsRepository  implements AttachmentRepositoryInterface{

   /**
    * Summary of all
    * @return \Illuminate\Database\Eloquent\Collection<int, Attachment>
    */
   public function all()
    {
        return Attachment::all();
    }

    /**
     * Summary of find
     * @param int $id
     * @return Attachment
     */
    public function find(int $id)
    {
        return Attachment::findOrFail($id);
    }


 
}