<?php

namespace App\Controllers;

class Login extends BaseController
{
  public function getIndex(): string
  {
    return view('login');
  }
}
