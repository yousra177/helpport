@extends('layouts.app')

@section('content')
<main class="container py-4">

  {{-- Alert for password change --}}
  @auth
    @if (!session()->has('password_alert_shown') && auth()->user()->created_at->isToday())
      <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-shield-alt me-2"></i>
        For security, please consider changing your password in
        <a href="{{ route('profile.edit') }}" class="fw-bold">Profile Settings</a>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                onclick="localStorage.setItem('password_alert_shown', '1')"></button>
      </div>
      @php session()->put('password_alert_shown', true) @endphp
    @endif
  @endauth

  <section class="post-feed">
    @forelse($problems as $problem)
      <article class="card mb-4 shadow-sm rounded-4 border-0">
        <div class="card-body">

          {{-- Post Header --}}
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
              <img src="{{ asset('frontend/assets/usericon.png') }}" alt="Profile"
                   class="rounded-circle me-3" width="48" height="48">
              <div>
                <strong>{{ $problem->user->name ?? 'Unknown User' }}</strong><br>
                <small class="text-muted">
                  {{ optional($problem->created_at)->diffForHumans() }}
                  @if($problem->type)
                    • <span class="badge bg-primary">{{ $problem->type }}</span>
                  @endif
                  @if(!$problem->approved)
                    • <span class="badge bg-warning text-dark">Pending</span>
                  @endif
                </small>
              </div>
            </div>

            @can('update', $problem)
              <div class="dropdown">
                <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <button class="dropdown-item text-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteProblemModal{{ $problem->id }}">
                      <i class="fas fa-trash me-2"></i> Delete
                    </button>
                  </li>
                </ul>
              </div>
            @endcan
          </div>

          {{-- Post Content --}}
          <h5 class="card-title">{{ $problem->title }}</h5>
          <p class="card-text text-truncate" style="max-width: 100%;">{{ $problem->description }}</p>

          {{-- Attachments --}}
          @if($problem->problem_attachments)
            @php
              $attachments = is_array($problem->problem_attachments)
                ? $problem->problem_attachments
                : json_decode($problem->problem_attachments, true);
            @endphp

            @if(!empty($attachments))
              <div class="mb-3">
                <h6><i class="fas fa-paperclip me-1"></i> Attachments</h6>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($attachments as $attachment)
                    <a href="{{ Storage::url($attachment) }}" target="_blank"
                       class="badge bg-light text-dark border">
                      <i class="fas fa-file me-1"></i>{{ basename($attachment) }}
                    </a>
                  @endforeach
                </div>
              </div>
            @endif
          @endif

          {{-- Actions --}}
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <a href="{{ route('solutions.index', ['problem' => $problem->id]) }}"
               class="btn btn-outline-primary btn-sm">
              <i class="fas fa-lightbulb me-1"></i> View Solutions
              <span class="badge bg-primary ms-1">{{ $problem->solutions_count ?? 0 }}</span>
            </a>
          </div>

        </div>
      </article>

      {{-- Delete Modal --}}
      <div class="modal fade" id="deleteProblemModal{{ $problem->id }}" tabindex="-1"
           aria-labelledby="deleteModalLabel{{ $problem->id }}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="{{ route('problems.destroy', $problem->id) }}" method="POST">
              @csrf
              @method('DELETE')
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $problem->id }}">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p class="mb-3 text-danger fw-semibold">Are you sure you want to delete this problem?</p>
                <textarea name="delete_reason" class="form-control" placeholder="Reason for deletion..." required></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash me-1"></i> Delete
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="text-center py-5">
        <i class="far fa-folder-open fa-3x mb-3 text-muted"></i>
        <p class="h5 text-muted">No posts available yet.</p>
      </div>
    @endforelse
  </section>

</main>
@endsection
