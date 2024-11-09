<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectMembers extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'project_id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
        'null' => false,
      ],
      'student_id' => [
        'type' => 'INT',
        'unsigned' => true,
        'null' => false,
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => false,
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

    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');

    $this->forge->createTable('project_members');
  }

  public function down()
  {
    $this->forge->dropTable('project_members');
  }
}
