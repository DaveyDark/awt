<?php

namespace App\Controllers;

class Home extends BaseController
{
  public function getIndex(): string
  {
    $session = session();
    $role = $session->get('role');
    if ($role === 'student') {
      $model = new \App\Models\StudentModel();
      $student = $model->find($session->get('id'));
      return view('projects', [
        'name' => $student['name'],
      ]);
    }
  }
}
