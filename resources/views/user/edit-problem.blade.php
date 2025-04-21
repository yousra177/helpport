@extends('layouts.app')

@section('content')

<main>

@yield('content')
  <div class="container mt-4">
    <h2 class="text-center">Edit Problem</h2>

    <!-- Success/Error Messages -->
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(isset($problem))
    <form method="POST" action="{{ route('problems.update', $problem->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <!-- Title -->
      <div class="mb-3">
        <label for="title" class="form-label">Title:</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $problem->title) }}" required>
      </div>

      <!-- Problem Type -->
      <div class="mb-3">
        <label for="type" class="form-label">Type of Problem:</label>
        <select name="type" id="type" class="form-select" required>
          <option value="" disabled>Choose a type of problem</option>
          @foreach ([
            'Software Application Issues' => 'Software & Application Issues',
            'Network Internet Problems' => 'Network & Internet Problems',
            'Database Data Management Problems' => 'Database & Data Management Problems',
            'Security Access Control Problems' => 'Security & Access Control Problems',
            'Hardware Equipment Issues' => 'Hardware & Equipment Issues',
            'IT Support Service Requests' => 'IT Support & Service Requests',
            'Project Collaboration Problems' => 'Project & Collaboration Problems'
          ] as $value => $label)
            <option value="{{ $value }}" {{ $problem->type == $value ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <!-- Description -->
      <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea name="description" id="description" rows="4" class="form-control" required>{{ old('description', $problem->description) }}</textarea>
      </div>

      <!-- File Upload -->
      <div class="mb-3">
    <label class="form-label">Attachments:</label>
    <input type="file" name="problem_attachments[]" class="form-control" accept="image/*, .pdf, .zip" multiple>

    @if($problem->problem_attachments)
        <div class="mt-2">
            <p>Current Attachments:</p>
            @foreach(json_decode($problem->problem_attachments, true) as $file)
                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="d-block">
                    <i class="fas fa-file"></i> View File
                </a>
            @endforeach
        </div>
    @endif
</div>


      <!-- Status -->
      <div class="mb-3">
        <label for="status" class="form-label">Problem Status:</label>
        <select name="status" id="status" class="form-select" required>
          <option value="solved" {{ $problem->status == 'solved' ? 'selected' : '' }}>Solved</option>
          <option value="unsolved" {{ $problem->status == 'unsolved' ? 'selected' : '' }}>Unsolved</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div class="text-center">
        <button type="submit" class="btn btn-success">
          <i class="fas fa-paper-plane"></i> Update
        </button>
      </div>
    </form>
    @else
      <div class="alert alert-danger text-center">
        <p>Problem not found!</p>
      </div>
    @endif
  </div>
</main>
@endsection
