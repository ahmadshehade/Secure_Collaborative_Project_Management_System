<?php 
 
 namespace  App\Interfaces\Repositories\Auth;

 interface  AuthenticateRepositoryInterface {
      public function  register(array $data);
    public function findByEmail(string $email);
 }