@extends('layouts.app')

@section('content')
<main>

  <div class="container mt-4">
    <!-- User Form -->
    <div class="card p-4 shadow-sm">
      <h5>Create User</h5>
      <form method="POST" action="{{ url('head/create-user') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" name="name" placeholder="Name" required>
          </div>
          <div class="col-md-6">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="col-md-6">
            <input type="text" class="form-control" name="phone_number" placeholder="Phone Number" required>
          </div>
          <div class="col-md-6">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="col-md-6">
            <label for="role">Role:</label>
            <select class="form-select" name="role" id="role" required>

              <option value="it_user">IT User</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="departement">Department:</label>
            <select class="form-select" name="departement" id="departement" required>
              <option value="{{ Auth::user()->departement }}" selected>
                {{ Auth::user()->departement }}
              </option>
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Submit</button>
          </div>
        </div>
    </form>
    <!-- Success/Error Messages -->
    @if(session('success'))
      <p class="text-success">{{ session('success') }}</p>
    @endif

    @if($errors->any())
      <p class="text-danger">Error: {{ $errors->first() }}</p>
    @endif
  </div>
  </div>

  <!-- Search Bar -->
<div class="container mt-4 d-flex justify-content-center">
    <div class="card p-3 shadow-sm" style="max-width: 500px; width: 100%;">
        <form method="GET" action="{{ url('head/create-user') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search user..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

    <!-- User List Table -->
<div class="container mt-4">
  <div class="card p-4 shadow-sm">
    <h5>User List</h5>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Role</th>
            <th>Department</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $data)
          <tr id="user-{{ $data->id }}">
            <td>{{ $data->name }}</td>
            <td>{{ $data->email }}</td>
            <td>{{ $data->phone_number }}</td>
            <td>{{ $data->role }}</td>
            <td>{{ $data->departement }}</td>
            <td>
              <a href="{{ url('head/users/' . $data->id . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
              <button type="button" onclick="deleteUser('{{ $data->id }}')" class="btn btn-danger btn-sm">Delete</button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

</main>
@endsection
