<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\StudentModel;
use App\Models\TeacherModel;

class Register extends BaseController
{
  public function getIndex()
  {
    // Check if user is already logged in
    $session = session();
    if ($session->get('role')) {
      return redirect()->to('/');
    }

    helper(['form']);

    return view('register');
  }

  public function postIndex()
  {
    helper(['form']);

    $role = $this->request->getPost('role');

    $data = $this->request->getPost([
      'name',
      'email',
      'password',
      'confirmPassword'
    ]);

    $model = null;
    if ($role === 'student') {
      $data['urn'] = $this->request->getPost('urn');
      $data['crn'] = $this->request->getPost('crn');
      $data['branch'] = $this->request->getPost('branch');

      $model = new StudentModel();
    } else {
      $model = new TeacherModel();
    }

    // Controller-based validation rules
    $validationRules = [
      'name'            => 'required|string|max_length[100]',
      'email'           => 'required|valid_email',
      'password'        => 'required|min_length[8]',
      'confirmPassword' => 'required|matches[password]'
    ];

    $validationMessages = [
      'confirmPassword' => [
        'matches' => 'The password confirmation does not match.'
      ]
    ];

    // Validate using controller rules
    if (! $this->validate($validationRules, $validationMessages)) {
      return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Remove confirmPassword before saving
    unset($data['confirmPassword']);

    // Validate and store data using model validation
    if ($model->save($data)) {
      return redirect()->to('/login');
    } else {
      // Return model validation errors
      return redirect()->back()->withInput()->with('errors', $model->errors());
    }
  }
}
