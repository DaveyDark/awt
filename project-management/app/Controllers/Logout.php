<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Logout extends BaseController
{
  public function getIndex(): ResponseInterface
  {
    $session = session();
    $session->destroy();
    return redirect()->to('/login');
  }
}
