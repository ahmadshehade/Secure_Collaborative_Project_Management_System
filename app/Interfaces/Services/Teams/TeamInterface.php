<?php

namespace App\Interfaces\Services\Teams;

interface TeamInterface
{
      public function index();

      public function show($id);

      public function store($request);

      public function   update($teamId, $request);

      public function destroy($teamId);

      public function getMostActiveTeams(int $limit = 10);
}
