<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ProjectModel;
use App\Models\ProjectMemberModel;
use App\Models\ProjectSubmissionModel;
use App\Models\TeacherModel;

class ProjectsSeeder extends Seeder
{
  public function run()
  {
    $db = \Config\Database::connect();

    // Get teacher ID
    $teacherModel = new TeacherModel();
    $teacher = $teacherModel->where('email', 'teacher@gndec.ac.in')->first();
    $teacherId = $teacher['id'];

    // Project Data
    $projects = [
      [
        'title'       => 'AI Chatbot',
        'description' => 'A chatbot project using natural language processing.',
        'status'      => 'active',
        'assigned'    => date('Y-m-d', strtotime('2024-01-10')),
        'due'         => date('Y-m-d', strtotime('2024-04-15')),
        'teacher_id'  => $teacherId,
        'student_id'  => '1',
        'internal_remarks' => 'Initial review pending',
        'external_remarks' => null,
        'created_at'  => date('Y-m-d H:i:s'),
      ],
      [
        'title'       => 'E-Learning Platform',
        'description' => 'An online learning platform with interactive features.',
        'status'      => 'in review',
        'assigned'    => date('Y-m-d', strtotime('2024-02-01')),
        'due'         => date('Y-m-d', strtotime('2024-06-01')),
        'teacher_id'  => $teacherId,
        'student_id'  => '2',
        'internal_remarks' => null,
        'external_remarks' => null,
        'created_at'  => date('Y-m-d H:i:s'),
      ]
    ];

    // Insert Projects
    $projectModel = new ProjectModel();
    $projectModel->insertBatch($projects);

    // Fetch project IDs
    $projectIds = $db->table('projects')
      ->whereIn('title', ['AI Chatbot', 'E-Learning Platform'])
      ->get()
      ->getResultArray();

    // Project Members Data
    $projectMembers = [
      [
        'project_id'  => $projectIds[0]['id'], // AI Chatbot project
        'student_id'  => '1',            // Devesh Sharma
        'created_at'  => date('Y-m-d H:i:s'),
      ],
      [
        'project_id'  => $projectIds[0]['id'], // AI Chatbot project
        'student_id'  => '2',
        'created_at'  => date('Y-m-d H:i:s'),
      ],
      [
        'project_id'  => $projectIds[1]['id'], // E-Learning Platform project
        'student_id'  => '2',
        'created_at'  => date('Y-m-d H:i:s'),
      ]
    ];

    // Insert Project Members
    $projectMemberModel = new ProjectMemberModel();
    $projectMemberModel->insertBatch($projectMembers);

    // Project Submission Data (only for "AI Chatbot" project)
    $projectSubmissions = [
      [
        'project_id'  => $projectIds[0]['id'],
        'file'        => 'path/to/ai_chatbot_report.pdf',
        'created_at'  => date('Y-m-d H:i:s'),
      ]
    ];

    // Insert Project Submission
    $projectSubmissionModel = new ProjectSubmissionModel();
    $projectSubmissionModel->insertBatch($projectSubmissions);
  }
}
