<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectSubmissions extends Migration
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
      ],
      'file' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
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

    $this->forge->createTable('project_submissions');
  }

  public function down()
  {
    $this->forge->dropTable('project_submissions');
  }
}
