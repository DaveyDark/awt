<?php

namespace App\Models;

use CodeIgniter\Model;

class Todo extends Model
{
  protected $table            = 'todos';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes   = true;
  protected $allowedFields    = ['task', 'done'];
  protected $useTimestamps = true;
  protected $validationRules = [
    'task' => 'required|min_length[3]|max_length[255]',
  ];
}
