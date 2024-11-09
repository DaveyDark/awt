<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjectMemberModel;
use App\Models\ProjectModel;
use App\Models\StudentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Projects extends BaseController
{
  public function getNew()
  {
    $session = session();
    $studentModel = new StudentModel();
    $student = $studentModel->find($session->get('id'));
    if (!$student) {
      return redirect()->to('/login')->with('error', 'Please login to continue');
    }

    return view('project_form', [
      'name' => $student['name'],
      'student' => $student,
    ]);
  }

  public function postCreate()
  {
    $db = \Config\Database::connect();
    $session = session();
    $studentModel = new StudentModel();
    $projectModel = new ProjectModel();
    $projectMemberModel = new ProjectMemberModel();

    $student = $studentModel->find($session->get('id'));
    $data = [
      'title' => $this->request->getPost('title'),
      'description' => $this->request->getPost('description'),
      'status' => 'in review',
      'student_id' => $student['id'],
    ];

    // Start transaction
    $db->transStart();

    // Insert project
    if (!$projectModel->insert($data)) {
      log_message('error', 'Project insert failed: ' . json_encode($projectModel->errors()));
      return redirect()->back()->withInput()->with('errors', $projectModel->errors());
    }

    // Add members to the project
    $projectId = $projectModel->getInsertID();
    $members = json_decode($this->request->getPost('members'));

    foreach ($members as $member) {
      if (!$projectMemberModel->insert([
        'project_id' => $projectId,
        'student_id' => $member,
      ])) {
        log_message('error', 'Project member insert failed for student_id ' . $member . ': ' . json_encode($projectMemberModel->errors()));
        return redirect()->back()->withInput()->with('errors', $projectMemberModel->errors());
      }
    }

    // Complete the transaction
    $db->transComplete();

    // Check for transaction success
    if ($db->transStatus() === false) {
      log_message('error', 'Transaction failed for project creation.');
      return redirect()->back()->withInput()->with('error', 'Failed to create project. Transaction Failed');
    }

    return redirect()->to('/')->with('success', 'Project created successfully');
  }
}
