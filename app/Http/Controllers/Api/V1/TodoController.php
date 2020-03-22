<?php

namespace App\Http\Controllers\Api\V1;

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


    public function getList()
    {
        $list = $this->service->getList();
        return response()->json($list);
    }

    public function register(TodoRegisterRequest $request)
    {
        $this->service->register($request->all());
        return response()->json();
    }

    public function toggle($id)
    {
        $this->service->toggle($id);
        return response()->json();
    }

    public function delete($id)
    {
        $this->service->delete($id);
        return response()->json();
    }
}
