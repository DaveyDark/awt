<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
  protected $table            = 'teachers';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $useSoftDeletes   = true;
  protected $protectFields    = true;
  protected $allowedFields    = [
    'email',
    'name',
    'password',
    'created_at',
    'updated_at',
    'deleted_at'
  ];
  // Dates
  protected $useTimestamps = true;

  // Validation
  protected $validationRules      = [
    'email'    => 'required|valid_email|is_unique[teachers.email]',
    'name'     => 'required|string|max_length[100]',
    'password' => 'required|min_length[8]'
  ];
  protected $validationMessages   = [
    'email' => [
      'is_unique'   => 'This email address is already registered.',
      'valid_email' => 'Please provide a valid email.',
      'required'    => 'Email is required.'
    ],
    'name' => [
      'required' => 'Name is required.',
      'max_length' => 'Name must not exceed 100 characters.'
    ],
    'password' => [
      'required' => 'Password is required.',
      'min_length' => 'Password must be at least 8 characters.',
    ]
  ];

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert   = ['hashPassword'];
  protected $beforeUpdate   = ['hashPassword'];

  function hashPassword(array $data)
  {
    if (!isset($data['data']['password'])) {
      return $data;
    }
    $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    return $data;
  }
}
