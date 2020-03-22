@extends('layouts.default')
@section('title')
Todos - Register
@endsection
@section('contents')
<div class="container">
    <div class="row">
        <div class="col-4 offset-4">
            <h4>Sign Up</h4>
            <form action="{{ route('web-register') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="id">User Id</label>
                    <input type="text" class="form-control" id="id" name="name">
                    @error('name')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    @error('password')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password-confirmation">Password Confirmation</label>
                    <input type="password" class="form-control" id="password-confirmation" name="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>
@endsection
