<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudents extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type'           => 'INT',
        'constraint'     => 5,
        'unsigned'       => true,
        'auto_increment' => true,
      ],
      'urn' => [
        'type'       => 'VARCHAR',
        'constraint' => 7,
        'null'       => false,
      ],
      'email' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'null'       => false,
      ],
      'name' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'null'       => false,
      ],
      'crn' => [
        'type'       => 'VARCHAR',
        'constraint' => 7,
        'null'       => false,
      ],
      'branch' => [
        'type'       => 'ENUM',
        'constraint' => ['CSE', 'ECE', 'ME', 'CE', 'EE', 'IT'],
        'null'       => false,
      ],
      'password' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => false,
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'deleted_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);

    // Set URN as the primary key
    $this->forge->addPrimaryKey('id');
    $this->forge->addUniqueKey('urn');
    $this->forge->addUniqueKey('email');
    $this->forge->addUniqueKey('crn');

    $this->forge->createTable('students');
  }

  public function down()
  {
    $this->forge->dropTable('students');
  }
}
