<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
  protected $table            = 'admins';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = true;
  protected $allowedFields    = [
    'email',
    'password',
  ];

  // Dates
  protected $useTimestamps = true;

  // Validation
  protected $validationRules      = [
    'email'    => 'required|valid_email|is_unique[teachers.email]',
    'password' => 'required|min_length[8]'
  ];
  protected $validationMessages   = [
    'email' => [
      'is_unique'   => 'This email address is already registered.',
      'valid_email' => 'Please provide a valid email.',
      'required'    => 'Email is required.'
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
}
