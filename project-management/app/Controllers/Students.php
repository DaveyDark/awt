<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Students extends BaseController
{
  public function getIndex($urn)
  {
    $studentModel = new StudentModel();
    // Searching by URN will be the most common use case
    $student = $studentModel->where('urn', $urn)->first();

    if (!$student) {
      // Try to find by CRN
      $student = $studentModel->where('crn', $urn)->first();
    }
    if (!$student) {
      // Try to find by ID
      $student = $studentModel->find($urn);
    }
    if (!$student) {
      return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    unset($student['deleted_at']);
    unset($student['password']);

    // Set response content type to JSON
    return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
      ->setJSON($student);
  }
}
