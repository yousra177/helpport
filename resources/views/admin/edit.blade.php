@extends('layouts.app')

@section('content')

<main>

@yield('content')

  <!-- Form Section -->
  <div class="container-custom mt-4">
    <div class="card p-4 shadow-sm">
      <h3 class="text-center mb-4">Edit User</h3>
      <form method="POST" action="{{ route('admin.users.update', ['id' => $user->id]) }}">
      @csrf
        @method('PUT')
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
          <label for="phone_number" class="form-label">Phone Number</label>
          <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required>
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Role</label>
          <select class="form-select" name="role" id="role" required>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="chef_dep" {{ $user->role == 'chef_dep' ? 'selected' : '' }}>Chef Department</option>
            <option value="it_user" {{ $user->role == 'it_user' ? 'selected' : '' }}>IT User</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="departement" class="form-label">Department</label>
          <select class="form-select" name="departement" id="departement" required>
            <option value="general" {{ $user->departement == 'general' ? 'selected' : '' }}>Admin General</option>
            <option value="deeis" {{ $user->departement == 'deeis' ? 'selected' : '' }}>Département Études et Intégration Solutions Informatiques</option>
            <option value="diei" {{ $user->departement == 'diei' ? 'selected' : '' }}>Département Internet et Intranet</option>
            <option value="dda" {{ $user->departement == 'dda' ? 'selected' : '' }}>Département des Acquisitions</option>
            <option value="dadam" {{ $user->departement == 'dadam' ? 'selected' : '' }}>Département Administration des Applications Métiers</option>
          </select>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

</main>

@endsection
