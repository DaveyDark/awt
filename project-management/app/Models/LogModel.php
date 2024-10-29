<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
  protected $table            = 'logs';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;

  protected $allowedFields    = [
    'user_id',
    'user_type',
    'action',
    'created_at'
  ];

  // Enable only creation timestamp
  protected $useTimestamps = true;
  protected $createdField  = 'created_at';

  // Disable soft deletes and update timestamps for immutability
  protected $useSoftDeletes = false;
  protected $updatedField   = null;
  protected $deletedField   = null;

  // Validation rules
  protected $validationRules = [
    'user_id'   => 'required|integer|validUser',
    'user_type' => 'required|in_list[admin,teacher,student]',
    'action'    => 'required|string|max_length[100]'
  ];

  protected $validationMessages = [
    'user_id' => [
      'required' => 'User ID is required.',
      'integer'  => 'User ID must be an integer.',
      'validUser' => 'User ID does not exist for the given user type.'
    ],
    'user_type' => [
      'required' => 'User type is required.',
      'in_list'  => 'User type must be one of: admin, teacher, or student.'
    ],
    'action' => [
      'required'   => 'Action description is required.',
      'max_length' => 'Action cannot exceed 100 characters.'
    ]
  ];

  // Register custom validation rule
  protected function validUser(string $str, string $fields, array $data): bool
  {
    if (!isset($data['user_id']) || !isset($data['user_type'])) {
      return false;
    }

    $userId = $data['user_id'];
    $userType = $data['user_type'];

    // Check existence of user ID based on user type
    switch ($userType) {
      case 'student':
        $studentModel = new \App\Models\StudentModel();
        return (bool) $studentModel->where('urn', $userId)->first();

      case 'teacher':
        $teacherModel = new \App\Models\TeacherModel();
        return (bool) $teacherModel->where('id', $userId)->first();

      case 'admin':
        $adminModel = new \App\Models\AdminModel();
        return (bool) $adminModel->where('id', $userId)->first();

      default:
        return false;
    }
  }

  // Make table immutable by preventing updates and deletes
  public function update($id = null, $data = null): bool
  {
    throw new \CodeIgniter\Exceptions\ModelException('Updating records is not allowed in LogModel');
  }

  public function delete($id = null, bool $purge = false): bool
  {
    throw new \CodeIgniter\Exceptions\ModelException('Deleting records is not allowed in LogModel');
  }
}
