<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class AuthFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    $session = session();

    // Check if the user is logged in
    if (! $session->get('role')) {
      return redirect()->to('/login')->with('error', 'You must be logged in to view this page.');
    }

    // Check if role is required and does not match the user's session role
    $requiredRole = $arguments[0] ?? null;
    if ($requiredRole && $session->get('role') !== $requiredRole) {
      // Generate the unauthorized view as a response
      $response = Services::response();
      $response->setStatusCode(403); // Set the HTTP status code for unauthorized access
      $response->setBody(view('unauthorized')); // Load the unauthorized view

      return $response;
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
