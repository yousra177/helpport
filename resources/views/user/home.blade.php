@extends('layouts.app')

@section('content')
<main class="container mt-4">

  {{-- Alert for password change --}}
  @auth
    @if (!session()->has('password_alert_shown') && auth()->user()->created_at->isToday())
      <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-shield-alt me-2"></i>
        For security, please consider changing your password in
        <a href="{{ route('profile.edit') }}" class="fw-bold">Profile Settings</a>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                onclick="localStorage.setItem('password_alert_shown', '1')"></button>
      </div>
      @php session()->put('password_alert_shown', true) @endphp
    @endif
  @endauth

  {{-- Pending approval alert --}}
  @if(session('pending_approval'))
    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
      <i class="fas fa-hourglass-half me-2"></i>
      {{ session('pending_approval') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
{{-- Problems Waiting for Approval --}}
@if($waitingProblems->isNotEmpty())
  <div class="mb-4">
    <h5 class="text-secondary mb-3">
      <i class="fas fa-clock me-2 text-warning"></i>Waiting for Approval
    </h5>
    @foreach ($waitingProblems as $pending)
      <div class="card mb-3 border-start border-warning border-3 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <strong class="d-block">{{ $pending->title }}</strong>
              <small class="text-muted">
                <i class="far fa-clock me-1"></i>{{ $pending->created_at->diffForHumans() }}
              </small>
            </div>
            <span class="badge bg-warning text-dark">Pending</span>
          </div>
          <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($pending->description, 100) }}</p>

          {{-- Edit/Delete if allowed --}}
          <div class="d-flex gap-2">
            @can('update', $pending)
              <a href="{{ route('problems.edit', $pending->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-edit me-1"></i>Edit
              </a>
            @endcan

            @can('delete', $pending)
              <form action="{{ route('problems.destroy', $pending->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this pending problem?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="fas fa-trash-alt me-1"></i>Delete
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

  <div class="row">
    {{-- Problem Feed --}}
    <div class="col-lg-8">
      <section class="post-feed">
        @if ($problems->isEmpty())
          <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i> No problems found. Be the first to post!
          </div>
        @else
          @foreach ($problems as $problem)
            <article class="card mb-4 shadow-sm">
              <div class="card-body">

                {{-- Post Header --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="d-flex align-items-center">
                    <img src="{{ asset('frontend/assets/usericon.png') }}" alt="User profile" class="rounded-circle me-3" width="48" height="48">
                    <div>
                      <strong class="d-block">{{ $problem->user->name ?? 'Unknown' }}</strong>
                      <small class="text-muted">
                        <i class="far fa-clock me-1"></i>{{ $problem->created_at->diffForHumans() }}
                        @if ($problem->type)
                          <span class="mx-2">•</span>
                          <span class="badge bg-primary">{{ $problem->type }}</span>
                        @endif
                      </small>
                    </div>
                  </div>

                  @can('update', $problem)
                    <div class="dropdown">
                      <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <a class="dropdown-item" href="{{ route('problems.edit', $problem->id) }}">
                            <i class="fas fa-edit me-2"></i>Edit
                          </a>
                        </li>
                        <li>
                          <form action="{{ route('problems.destroy', $problem->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this problem?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                              <i class="fas fa-trash me-2"></i>Delete
                            </button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  @endcan
                </div>

                {{-- Problem Content --}}
                <h5 class="mb-2">{{ $problem->title }}</h5>
                <p class="text-muted mb-3">{{ $problem->description }}</p>

                {{-- Attachments --}}
                @if ($problem->problem_attachments)
                  @php
                    $attachments = is_array($problem->problem_attachments)
                      ? $problem->problem_attachments
                      : json_decode($problem->problem_attachments, true);
                  @endphp
                  @if (!empty($attachments))
                    <div class="mb-3">
                      <h6 class="text-muted"><i class="fas fa-paperclip me-1"></i>Attachments</h6>
                      <div class="d-flex flex-wrap gap-2">
                        @foreach ($attachments as $attachment)
                          <a href="{{ Storage::url($attachment) }}" target="_blank" class="text-decoration-none border rounded px-2 py-1 small">
                            <i class="fas fa-file me-1"></i>{{ basename($attachment) }}
                          </a>
                        @endforeach
                      </div>
                    </div>
                  @endif
                @endif

                {{-- Actions --}}
                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                  <a href="{{ route('solutions.index', $problem->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-lightbulb me-1"></i> View Solutions
                    <span class="badge bg-primary ms-1">{{ $problem->solutions_count ?? 0 }}</span>
                  </a>
                </div>

              </div>
            </article>
          @endforeach

          {{-- Pagination --}}
          @if ($problems->hasPages())
            <div class="d-flex justify-content-center mt-4">
              {{ $problems->links() }}
            </div>
          @endif
        @endif
      </section>
    </div>
  </div>

  {{-- Floating Action Button --}}
  <a href="{{ url('/problems/create') }}"
     class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg"
     aria-label="Create a new problem"
     title="Create a new problem">
    <i class="fas fa-plus"></i>
  </a>

</main>
@endsection
