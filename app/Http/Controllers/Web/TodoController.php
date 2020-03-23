<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRegisterRequest;
use App\Services\TodoService;
use Illuminate\Http\Request;

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

    public function showTodoPage(Request $request)
    {
        $todos = $this->service->getList();
        $token = $request->user()->api_token;

        return view('todo')->with(compact('todos', 'token'));
    }

    public function register(TodoRegisterRequest $request)
    {
        $this->service->register($request->all());
        return response()->redirectToRoute('web-view-todo-page');
    }

    public function toggle($id)
    {
        $this->service->toggle($id);
        return response()->redirectToRoute('web-view-todo-page');
    }

    public function delete($id)
    {
        $this->service->delete($id);
        return response()->redirectToRoute('web-view-todo-page');
    }
}
