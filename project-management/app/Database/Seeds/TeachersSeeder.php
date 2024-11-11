<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TeacherModel;

class TeachersSeeder extends Seeder
{
  public function run()
  {
    $teachers = [
      [
        'email'      => 'teacher1@gndec.ac.in',
        'name'       => 'Example Teacher1',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
      [
        'email'      => 'teacher2@gndec.ac.in',
        'name'       => 'Example Teacher2',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
      [
        'email'      => 'teacher3@gndec.ac.in',
        'name'       => 'Example Teacher3',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
    ];

    $model = new \App\Models\TeacherModel();
    $model->insertBatch($teachers);
  }
}
