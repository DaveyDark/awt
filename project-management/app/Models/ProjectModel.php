<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
  protected $table            = 'projects';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = true;
  protected $protectFields    = true;
  protected $allowedFields    = [
    'title',
    'description',
    'status',
    'assigned',
    'due',
    'submitted',
    'completed',
    'teacher_id',
    'student_id',
    'internal_remarks',
    'external_remarks',
    'created_at',
    'updated_at',
    'deleted_at'
  ];

  protected bool $allowEmptyInserts = false;
  protected bool $updateOnlyChanged = true;

  // Dates
  protected $useTimestamps = true;

  // Validation
  protected $validationRules      = [
    'title' => 'required|min_length[3]|max_length[100]',
    'description' => 'required',
    'status' => 'required|in_list[active,in review,submitted,completed]',
  ];
  protected $validationMessages   = [
    'title' => [
      'required' => 'Title is required',
      'min_length' => 'Title must be at least 3 characters long',
      'max_length' => 'Title must not exceed 100 characters'
    ],
    'description' => [
      'required' => 'Description is required'
    ],
    'status' => [
      'required' => 'Status is required',
      'in_list' => 'Invalid status'
    ]
  ];
  protected $skipValidation       = false;
  protected $cleanValidationRules = true;

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert   = [];
  protected $afterInsert    = [];
  protected $beforeUpdate   = [];
  protected $afterUpdate    = [];
  protected $beforeFind     = [];
  protected $afterFind      = [];
  protected $beforeDelete   = [];
  protected $afterDelete    = [];
}
