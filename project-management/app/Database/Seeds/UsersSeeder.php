<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
  public function run()
  {
    $this->call('StudentsSeeder');
    $this->call('TeachersSeeder');
    $this->call('AdminsSeeder');
  }
}
