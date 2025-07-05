<?php 
namespace App\Interfaces\Services\Users;

interface UserInterface{

    public function  updateRole($id,$request);


    public function destroyUser($id);



    public function getAllUser($limit);

    public function show($id);
}