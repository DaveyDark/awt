<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogs extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'user_id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
        'null' => false
      ],
      'user_type' => [
        'type' => 'ENUM',
        'constraint' => ['admin', 'teacher', 'student'],
        'null' => false
      ],
      'action' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => false
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true
      ]
    ]);

    $this->forge->addPrimaryKey('id');

    $this->forge->createTable('logs');
  }

  public function down()
  {
    $this->forge->dropTable('log');
  }
}
