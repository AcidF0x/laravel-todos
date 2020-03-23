@extends('layouts.default')
@section('title')
    Todos
@endsection
@section('contents')
    <div class="container" style="margin-top: 20px">
        <div class="row">
            <div class="col-6 offset-3">
                <div class="row">
                    <div class="col-10">
                        <h4>Todos...</h4>
                    </div>
                    <div class="col-2 text-right">
                        <a href="{{ route('web-logout') }}" class="btn btn-sm btn-danger">Logout</a>
                    </div>
                    <div class="col-12">
                        <small>API TOKEN</small>
                        <small>{{ $token }}</small>
                    </div>
                </div>
                <form id="form" action="{{ route('web-register-todo') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="todo">Todo..</label>
                        <input type="text" class="form-control" id="todo" name="name">
                    </div>
                    @error('todo')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </form>
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Todo</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todos as $todo)
                            <tr>
                                <td>
                                    @if($todo->is_activate)
                                        {{ $todo->name }}
                                    @else
                                        <s>{{ $todo->name }}</s>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-right">
                                        <a href="{{ route('web-toggle-todo', $todo->id)}}" class="btn btn-sm btn-info">
                                            {{ $todo->is_activate ? 'Done' : 'ReActive'}}
                                        </a>
                                        <a href="{{ route('web-delete-todo', $todo->id)}}" class="btn btn-sm btn-danger">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
