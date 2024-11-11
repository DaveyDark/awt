<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\ProjectModel;
use App\Models\ProjectMemberModel;
use App\Models\TeacherModel;

class Home extends BaseController
{
  public function getIndex()
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
    $projects = [];
    $name = '';

    if ($role === 'student') {
      // Retrieve the student's details
      $student = $studentModel->find($session->get('id'));

      if (!$student) {
        return redirect()->to('/login')->with('error', 'Please login to continue');
      }
      $name = $student['name'];

      // Get project IDs for projects where the student is a member
      $projectIds = $projectMemberModel
        ->where('student_id', $student['id'])
        ->findColumn('project_id');

      // Retrieve project details for the student’s projects
      $projects = $projectModel->whereIn('id', $projectIds)->findAll();
    } else if ($role === 'teacher') {
      // Retrieve the teacher's details
      $teacher = $teacherModel->find($session->get('id'));
      if (!$teacher) {
        return redirect()->to('/login')->with('error', 'Please login to continue');
      }
      $name = $teacher['name'];

      // Retrieve projects where the teacher is assigned
      $projects = $projectModel->where('teacher_id', $teacher['id'])->findAll();
    } else {
      // Admin role
      // Show all projects
      $projects = $projectModel->findAll();
      $name = 'Admin';
    }

    // Process each project to add members count and teacher name
    foreach ($projects as &$project) {
      // Count the number of members in this project
      $project['members'] = $projectMemberModel->where('project_id', $project['id'])->countAllResults();

      // Get teacher name if a teacher is assigned
      if (!empty($project['teacher_id'])) {
        $teacher = $teacherModel->find($project['teacher_id']);
        $project['teacher'] = $teacher ? $teacher['name'] : 'Not Assigned';
      } else {
        $project['teacher'] = 'Not Assigned';
      }
    }

    return view('dashboard', [
      'name' => $name,
      'projects' => $projects,
    ]);
  }
}
