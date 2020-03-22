<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRegisterRequest;
use App\Services\TodoService;

class TodoController extends Controller
{
    /**
     * @var TodoService
     */
    private $service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->service = resolve(TodoService::class);
            return $next($request);
        });
    }

    public function showTodoPage()
    {
        $todos = $this->service->getList();
        return view('todo')->with(compact('todos'));
    }

    public function register(TodoRegisterRequest $request)
    {
        $this->service->register($request->all());
        return back();
    }

    public function toggle($id)
    {
        $this->service->toggle($id);
        return back();
    }

    public function delete($id)
    {
        $this->service->delete($id);
        return back();
    }
}
