<?php

namespace App\Controllers;

class Home extends BaseController
{
  public function index(): string
  {
    $todo = new \App\Models\Todo();
    $todos = $todo->orderBy('done', 'asc')->orderBy('created_at', 'desc')->findAll();
    return view('home', ['todos' => $todos]);
  }

  public function create()
  {
    $task = $this->request->getPost('task');
    $todo = new \App\Models\Todo();
    if ($todo->save(['task' => $task]))
      return redirect()->to('/')->with('success', 'Task created successfully');
    else
      return redirect()->to('/')->with('errors', $todo->errors());
  }

  public function toggle(int $id)
  {
    $todo = new \App\Models\Todo();
    $row = $todo->find($id);
    $row['done'] = !$row['done'];
    $todo->save($row);
    return response()->setStatusCode(200)->setBody(var_dump($row));
  }

  public function delete(int $id)
  {
    $todo = new \App\Models\Todo();
    $todo->delete($id);
    return redirect()->to('/')->with('success', 'Task deleted');
  }
}
