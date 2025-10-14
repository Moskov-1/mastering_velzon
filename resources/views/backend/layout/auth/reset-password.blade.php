@extends('backend.layout.auth.auth-app')
@section('content')
<form action="{{route('auth.reset.finish')}}" method="post">
    @csrf
    <input type="text" hidden name="email" value="{{ $email }}">
    <div class="row mb-3">
        <div class="col-lg-3">
            <label for="nameInput" class="form-label">New Password</label>
        </div>
        <div class="col-lg-9">
            <input type="password" class="form-control" id="nameInput" name="password" >
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-3">
            <label for="nameInput" class="form-label">Confim Password</label>
        </div>
        <div class="col-lg-9">
            <input type="password" class="form-control" id="nameInput" name="password_confirmation">
        </div>
    </div>
    <div class="text-end">
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
@endsection