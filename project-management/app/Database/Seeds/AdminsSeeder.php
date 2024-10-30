<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\AdminModel;

class AdminsSeeder extends Seeder
{
  public function run()
  {
    $admins = [
      [
        'email'      => 'admin@gndec.ac.in',
        'password'   => password_hash('adminadmin', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
      ],
    ];

    $model = new AdminModel();
    $model->insertBatch($admins);
  }
}
