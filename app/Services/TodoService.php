<?php

namespace App\Services;

use App\Todo;
use App\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * @return Todo[]|Collection
     */
    public function getList()
    {
        return $this->user->todos;
    }

    /**
     * @param  array  $params
     */
    public function register(array $params)
    {
        $this->user->todos()->create($params);
    }

    /**
     * @param $todoId
     */
    public function toggle($todoId)
    {
        $todo = $this->user->todos()->findOrFail($todoId);
        $todo->is_activate = !$todo->is_activate;
        $todo->save();
    }

    /**
     * @param $todoId
     * @throws \Exception
     */
    public function delete($todoId)
    {
        $todo = $this->user->todos()->findOrFail($todoId);
        $todo->delete();
    }
}
