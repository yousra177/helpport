@extends('layouts.app')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-sm p-4" style="max-width: 700px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-4">Create a Problem</h2>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('problems/create') }}" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label">Title:</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <!-- Problem Type -->
                <div class="mb-3">
                    <label for="type" class="form-label">Type of Problem:</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="" disabled selected>Select a problem type</option>
                        <option value="Software Application Issues">Software & Application Issues</option>
                        <option value="Network Internet Problems">Network & Internet Problems</option>
                        <option value="Database Data Management Problems">Database & Data Management Problems</option>
                        <option value="Security Access Control Problems">Security & Access Control Problems</option>
                        <option value="Hardware Equipment Issues">Hardware & Equipment Issues</option>
                        <option value="IT Support Service Requests">IT Support & Service Requests</option>
                        <option value="Project Collaboration Problems">Project & Collaboration Problems</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" rows="4" class="form-control" required></textarea>
                </div>

                <!-- Attachments -->
                <div class="mb-3">
                    <label for="problem_attachments" class="form-label">Attachments:</label>
                    <input type="file" name="problem_attachments[]" id="problem_attachments"
                           class="form-control" accept="image/*,video/*,.pdf,.docx" multiple>
                </div>

                <!-- Hidden Approval -->
                <input type="hidden" name="approved" value="true">

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-paper-plane me-2"></i> Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
