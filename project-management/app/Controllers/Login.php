<?php

namespace App\Controllers;

class Login extends BaseController
{
  public function getIndex(): string
  {
    // Check if user is already logged in
    $session = session();
    if ($session->get('role')) {
      return redirect()->to('/');
    }

    return view('login');
  }

  public function postIndex()
  {
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $user = null;
    $role = null;

    $studentModel = new \App\Models\StudentModel();
    $teacherModel = new \App\Models\TeacherModel();
    $adminModel = new \App\Models\AdminModel();

    // Try to find the student by email
    $user = $studentModel->where('email', $email)->first();
    if ($user) $role = 'student';
    // If not found, try to find the teacher by email
    if (!$user) {
      $user = $teacherModel->where('email', $email)->first();
      if ($user) $role = 'teacher';
    }
    // If still not found, try to find the admin by email
    if (!$user) {
      $user = $adminModel->where('email', $email)->first();
      if ($user) $role = 'admin';
    }

    // If user is still null, return back with error
    if (!$user) {
      return redirect()->to('/login')->withInput()->with('error', 'Invalid email or password');
    }

    // Match the password
    if (!password_verify($password, $user['password'])) {
      return redirect()->to('/login')->withInput()->with('error', 'Invalid email or password');
    }

    // Set session data
    $session = session();
    $session->set([
      'role' => $role,
      'email' => $user['email'],
      'id' => $role === 'student' ? $user['urn'] : $user['id'],
    ]);

    return redirect()->to('/');
  }
}
