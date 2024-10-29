<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeachers extends Migration
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
      'email' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => false
      ],
      'name' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => false
      ],
      'password' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => false
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true
      ],
      'deleted_at' => [
        'type' => 'DATETIME',
        'null' => true
      ]
    ]);
    $this->forge->addPrimaryKey('id');
    $this->forge->addUniqueKey('email');

    $this->forge->createTable('teachers');
  }

  public function down()
  {
    $this->forge->dropTable('teachers');
  }
}
