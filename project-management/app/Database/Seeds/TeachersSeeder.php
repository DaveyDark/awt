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
        'email'      => 'teacher@gndec.ac.in',
        'name'       => 'Example Teacher',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
    ];

    $model = new \App\Models\TeacherModel();
    $model->insertBatch($teachers);
  }
}
