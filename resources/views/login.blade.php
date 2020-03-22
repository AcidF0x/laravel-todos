@extends('layouts.default')
@section('title')
    Todos - Login
@endsection
@section('contents')
    <div class="container">
        <div class="row">
            <div class="col-4 offset-4">
                <h4>Login</h4>
                <form action="{{ route('web-login') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="id">User Id</label>
                        <input type="text" class="form-control" id="id" name="name">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="{{route('web-view-register-page')}}" role="button" class="btn btn-info">SignUp</a>
                    @error('login')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </form>
            </div>
        </div>
    </div>
@endsection
