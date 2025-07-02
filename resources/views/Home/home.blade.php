@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Welcome, {{ auth()->user()->name }}!</p>
            {{-- <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Users</a> --}}
        </div>
    </div>
@endsection
