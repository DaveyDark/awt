<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjectMemberModel;
use App\Models\ProjectModel;
use App\Models\ProjectSubmissionModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use CodeIgniter\HTTP\ResponseInterface;

class Projects extends BaseController
{
  public function getIndex($id)
  {
    $session = session();

    if (!isset($_SESSION['role'])) {
      return redirect()->to('/login')->with('error', 'Please login to continue');
    }

    $role = $session->get('role');
    $projectModel = new ProjectModel();
    $projectMemberModel = new ProjectMemberModel();
    $studentModel = new StudentModel();
    $teacherModel = new TeacherModel();
    $project = $projectModel->find($id);
    $name = '';

    if ($role === 'student') {
      // Retrieve the student's details
      $student = $studentModel->find($session->get('id'));

      if (!$student) {
        return redirect()->to('/login')->with('error', 'Please login to continue');
      }
      $name = $student['name'];
    } else if ($role === 'teacher') {
      // Retrieve the teacher's details
      $teacher = $teacherModel->find($session->get('id'));
      if (!$teacher) {
        return redirect()->to('/login')->with('error', 'Please login to continue');
      }
      $name = $teacher['name'];
    } else {
      // Admin role
      $name = 'Admin';
    }

    // Add additional info to project
    $project['members'] = $projectMemberModel->where('project_id', $project['id'])->countAllResults();

    // Get teacher name if a teacher is assigned
    if (!empty($project['teacher_id'])) {
      $teacher = $teacherModel->find($project['teacher_id']);
      $project['teacher'] = $teacher ? $teacher['name'] : 'Not Assigned';
    } else {
      $project['teacher'] = 'Not Assigned';
    }

    // Add project members
    $members = $projectMemberModel->where('project_id', $project['id'])->findAll();
    $project['members'] = [];
    foreach ($members as $member) {
      $student = $studentModel->find($member['student_id']);
      $project['members'][] = $student ? $student['name'] : 'Unknown';
    }

    // Add Project Files
    $projectSubmissionModel = new ProjectSubmissionModel();
    $submissions = $projectSubmissionModel->where('project_id', $project['id'])->findAll();
    $project['submissions'] = [];
    foreach ($submissions as $submission) {
      $submission['name'] = basename($submission['file']);
      $project['submissions'][] = $submission;
    }

    $teachers = $teacherModel->findAll();

    return view('project_details', [
      'name' => $name,
      'project' => $project,
      'teachers' => $teachers,
    ]);
  }
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

  public function postAssignTeacher($projectId)
  {
    $session = session();
    $role = $session->get('role');
    if ($role !== 'admin') {
      return redirect()->to('/')->with('error', 'Unauthorized Access');
    }
    $teacherId = $this->request->getPost('teacher');
    if (!$teacherId) {
      return redirect()->back()->with('error', 'Teacher ID is required');
    }
    $teacherModel = new TeacherModel();
    $projectModel = new ProjectModel();
    $project = $projectModel->find($projectId);

    // If project is not in review status, redirect back
    if ($project['status'] !== 'in review') {
      return redirect()->back()->with('error', 'Project is not in review status');
    }

    $teacher = $teacherModel->find($teacherId);
    if (!$teacher) {
      return redirect()->back()->with('error', 'Teacher not found');
    }
    $projectModel->update($projectId, ['teacher_id' => $teacher['id']]);
    return redirect()->to('/projects/' . $projectId)->with('success', 'Teacher assigned successfully');
  }

  public function postApprove($id)
  {
    // Approve the project, change status from in review to active
    $session = session();
    $role = $session->get('role');
    if ($role !== 'teacher') {
      return redirect()->back()->with('error', 'Unauthorized Access');
    }
    $projectModel = new ProjectModel();
    $project = $projectModel->find($id);
    if ($project['status'] !== 'in review') {
      return redirect()->back()->with('error', 'Project is not in review status');
    }
    $project['status'] = 'active';
    $project['assigned'] = date('Y-m-d H:i:s');
    $due = $this->request->getPost('due');
    if ($due) {
      $project['due'] = $due;
    } else {
      $project['due'] = date('Y-m-d', strtotime('+30 days'));
    }
    $projectModel->save($project);
    return redirect()->to('/projects/' . $id)->with('success', 'Project approved successfully');
  }

  public function postDeny($id)
  {
    // Deny the project, change status from in review to denied
    $session = session();
    $role = $session->get('role');
    if ($role !== 'teacher') {
      return redirect()->back()->with('error', 'Unauthorized Access');
    }
    $projectModel = new ProjectModel();
    $project = $projectModel->find($id);
    if ($project['status'] !== 'in review') {
      return redirect()->back()->with('error', 'Project is not in review status');
    }
    $projectModel->update($id, ['status' => 'denied']);
    return redirect()->to('/projects/' . $id)->with('success', 'Project denied successfully');
  }


  public function postSubmit($projectId)
  {
    $session = session();
    $role = $session->get('role');

    // Check if user is a student and the project is active
    if ($role !== 'student') {
      return redirect()->back()->with('error', 'Unauthorized Access');
    }

    // Fetch the project and verify its status
    $projectModel = new ProjectModel();
    $project = $projectModel->find($projectId);
    if ($project['status'] !== 'active') {
      return redirect()->back()->with('error', 'Project is not active');
    }

    // Load ProjectSubmissionModel to save file metadata
    $projectSubmissionModel = new ProjectSubmissionModel();
    $uploadedFiles = $this->request->getFiles();
    $uploadPath = WRITEPATH . 'uploads/projects/' . $projectId . '/';

    // Ensure the directory exists
    if (!is_dir($uploadPath)) {
      mkdir($uploadPath, 0777, true);
    }

    foreach ($uploadedFiles['files'] as $file) {
      if ($file->isValid() && !$file->hasMoved()) {
        // Move the file to the server directory
        $fileName = $file->getClientName();
        $file->move($uploadPath, $fileName);

        $projectSubmissionModel->insert([
          'project_id' => $projectId,
          'file' => $uploadPath . $fileName,
        ]);
      } else {
        return redirect()->back()->with('error', 'File upload failed');
      }
    }

    $project['status'] = 'submitted';
    $project['submitted'] = date('Y-m-d H:i:s');
    $projectModel->save($project);

    return redirect()->to('/projects/' . $projectId)->with('success', 'Files submitted successfully');
  }

  public function getDownload($projectId, $fileId)
  {
    $session = session();
    $role = $session->get('role');

    // Restrict access to students, teachers, and admins
    if (!in_array($role, ['student', 'teacher', 'admin'])) {
      return redirect()->back()->with('error', 'Unauthorized Access');
    }

    $projectSubmissionModel = new ProjectSubmissionModel();
    $projectModel = new ProjectModel();

    // Check if the project exists and retrieve file details
    $project = $projectModel->find($projectId);
    if (!$project) {
      return redirect()->back()->with('error', 'Project not found');
    }

    $file = $projectSubmissionModel->find($fileId);
    if (!$file || $file['project_id'] != $projectId) {
      return redirect()->back()->with('error', 'File not found');
    }

    $filePath = $file['file'];

    // Check if file exists on the server
    if (!file_exists($filePath)) {
      return redirect()->back()->with('error', 'File not found on server');
    }

    // Return file for download
    return $this->response->download($filePath, null)->setFileName(basename($filePath));
  }

  public function postRemarks($projectId)
  {
    $session = session();
    $role = $session->get('role');
    // Check if user is a teacher or admin
    if (!in_array($role, ['teacher', 'admin'])) {
      return redirect()->back()->with('error', 'Unauthorized Access');
    }
    $projectModel = new ProjectModel();
    $project = $projectModel->find($projectId);
    if (!$project) {
      return redirect()->back()->with('error', 'Project not found');
    }
    $project['internal_remarks'] = $this->request->getPost('internal_remarks');
    $project['external_remarks'] = $this->request->getPost('external_remarks');
    $project['completed'] = date('Y-m-d H:i:s');
    $project['status'] = 'completed';
    $projectModel->save($project);
    return redirect()->to('/projects/' . $projectId)->with('success', 'Remarks added successfully');
  }
}
