@extends('layouts.app')

@section('content')
<main class="container my-5">
    <div class="card shadow-sm rounded-4 p-4">
        <h2 class="text-center mb-4">My Account</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <!-- Personal Info -->
            <h5 class="mb-3">Personal Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">User Name</label>
                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name ?? 'N/A' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" class="form-control" name="phone_number" value="{{ Auth::user()->phone_number ?? 'N/A' }}">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1 fw-bold">Role:</p>
                    <p class="text-muted">{{ Auth::user()->role ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 fw-bold">Department:</p>
                    <p class="text-muted">{{ Auth::user()->departement ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Login Info -->
            <h5 class="mb-3">Login Credentials</h5>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" value="{{ Auth::user()->email ?? 'N/A' }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" name="current_password" placeholder="Enter your current password">
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter your new password">
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm your new password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
    </div>
</main>
@endsection
