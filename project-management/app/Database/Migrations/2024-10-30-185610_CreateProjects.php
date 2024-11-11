<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjects extends Migration
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
      'title' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => false,
      ],
      'description' => [
        'type' => 'TEXT',
        'null' => false,
      ],
      'status' => [
        'type' => 'ENUM',
        'constraint' => ['active', 'in review', 'submitted', 'completed', 'denied'],
        'default' => 'active',
        'null' => false,
      ],
      'assigned' => [
        'type' => 'DATE',
        'null' => true,
      ],
      'due' => [
        'type' => 'DATE',
        'null' => true,
      ],
      'submitted' => [
        'type' => 'DATE',
        'null' => true,
      ],
      'completed' => [
        'type' => 'DATE',
        'null' => true,
      ],
      'teacher_id' => [
        'type' => 'INT',
        'unsigned' => true,
        'null' => true,
      ],
      'student_id' => [
        'type' => 'INT',
        'unsigned' => true,
      ],
      'internal_remarks' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'external_remarks' => [
        'type' => 'TEXT',
        'null' => true,
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
    $this->forge->addForeignKey('teacher_id', 'teachers', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('projects');
  }

  public function down()
  {
    $this->forge->dropTable('projects');
  }
}
