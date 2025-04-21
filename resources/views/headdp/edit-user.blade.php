@extends('layouts.app')

@section('content')
<main>

  <div class="container-custom mt-5">
    <div class="form-container">
      <h4>Edit User Details</h4>
      <form method="POST" action="{{ url('head/users/' . $user->id . '/edit') }}">
        @csrf
        @method('PUT') <!-- Specify the PUT method for updating -->

        <div class="mb-3">
          <label for="name" class="form-label">Name:</label>
          <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3">
          <label for="phone_number" class="form-label">Phone Number:</label>
          <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required>
        </div>

        <div class="mb-3">
          <label for="role" class="form-label">Role:</label>
          <select class="form-select" name="role" id="role" required>
            <option value="it_user" {{ $user->role == 'it_user' ? 'selected' : '' }}>IT User</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="departement" class="form-label">Department:</label>
          <select class="form-select" name="departement" id="departement" required>
            <option value="{{ $user->departement }}" selected>{{ $user->departement }}</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
      </form>
    </div>
  </div>


</main>
@endsection
