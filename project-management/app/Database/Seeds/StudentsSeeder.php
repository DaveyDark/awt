<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\StudentModel;

class StudentsSeeder extends Seeder
{
  public function run()
  {
    $students = [
      [
        'urn'        => '2203818',
        'email'      => 'devesh2221037@gndec.ac.in',
        'name'       => 'Devesh Sharma',
        'crn'        => '2221037',
        'branch'     => 'IT',
        'password'   => password_hash('devesh811', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
      [
        'urn'        => '2203855',
        'email'      => 'mayank2221074@gndec.ac.in',
        'name'       => 'Mayank',
        'crn'        => '2221074',
        'branch'     => 'IT',
        'password'   => password_hash('mayank123', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
    ];

    $model = new StudentModel();
    $model->insertBatch($students);
  }
}
