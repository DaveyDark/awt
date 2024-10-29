<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
  protected $table            = 'students';
  protected $primaryKey       = 'urn';
  protected $useAutoIncrement = false;

  protected $allowedFields    = [
    'urn',
    'email',
    'name',
    'crn',
    'branch',
    'password',
    'created_at',
    'updated_at',
    'deleted_at'
  ];

  protected $useTimestamps = true;
  protected $useSoftDeletes = true;

  // Optionally, define validation rules for form input
  protected $validationRules = [
    'urn'     => 'required|exact_length[7]|is_unique[students.urn]',
    'email'   => 'required|valid_email|is_unique[students.email]',
    'name'    => 'required|string|max_length[100]',
    'crn'     => 'required|exact_length[7]|is_unique[students.crn]',
    'branch'  => 'required|in_list[CSE,ECE,ME,CE,EE,IT]',
    'password' => 'required|min_length[8]'
  ];

  protected $validationMessages = [
    'urn' => [
      'is_unique' => 'This URN is already registered.',
      'exact_length' => 'URN must be exactly 7 digits.',
      'required' => 'Please provide a URN.'
    ],
    'email' => [
      'required' => 'Please provide an email',
      'is_unique' => 'This email address is already registered.',
      'valid_email' => 'Please provide a valid email address.'
    ],
    'crn' => [
      'required' => 'Please provide a CRN.',
      'is_unique' => 'This CRN is already registered.',
      'exact_length' => 'CRN must be exactly 7 digits.'
    ],
    'password' => [
      'min_length' => 'Password must be at least 8 characters long.',
      'required' => 'Please provide a password.'
    ],
    'branch' => [
      'required' => 'Please select a branch.',
      'in_list' => 'Please select a valid branch.'
    ]
  ];

  // Hash password before insert or update
  protected $beforeInsert = ['hashPassword'];
  protected $beforeUpdate = ['hashPassword'];

  function hashPassword(array $data)
  {
    if (!isset($data['data']['password'])) {
      return $data;
    }
    $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    return $data;
  }
}
