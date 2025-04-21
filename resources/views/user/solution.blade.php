@extends('layouts.app')

@section('content')

<main class="container py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <div>
            <h1 class="h3 fw-bold">Solutions for: <strong>{{ $problem->title }}</strong></h1>
            <p class="text-muted small">Find existing solutions or contribute your own knowledge</p>
        </div>
        <span class="badge bg-primary px-3 py-2">{{ $problem->type }}</span>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Solution Form -->
    @php $userRole = Auth::user()->role; @endphp
    @if($userRole !== 'admin' && $userRole !== 'chef_dep')
    <section class="mb-5">
        <h2 class="h5 fw-bold mb-3"><i class="fas fa-lightbulb me-2"></i>Propose a Solution</h2>
        <form action="{{ route('solutions.store', $problem->id) }}" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
            @csrf
            <input type="hidden" name="problem_id" value="{{ $problem->id }}">

            <div class="mb-3">
                <label class="form-label">Solution Details</label>
                <textarea class="form-control" name="content" rows="6" required placeholder="Describe your solution..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Attachments</label>
                <input type="file" name="attachments[]" class="form-control" multiple>
                <small class="text-muted">Max 2MB. JPG, PNG, PDF, DOC, ZIP</small>
            </div>

            <div class="d-flex justify-content-between align-items-center">
               
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Submit
                </button>
            </div>
        </form>
    </section>
    @endif

    <!-- Solutions List -->
    <section>
        @forelse($solutions as $solution)
            <div class="card mb-4 shadow-sm">
                @if($solution->is_best)
                    <div class="position-absolute top-0 end-0 p-2">
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Best Solution</span>
                    </div>
                @endif

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ $solution->anonymous ? asset('frontend/assets/usericon.png') : ($solution->user->profile_pic ? Storage::url($solution->user->profile_pic) : asset('frontend/assets/usericon.png')) }}" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <div class="fw-semibold">{{ $solution->anonymous ? 'Anonymous' : $solution->user->name }}</div>
                                <div class="text-muted small">{{ $solution->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="btn-group">
                            @unless(in_array($userRole, ['admin', 'chef_dep']))
                                @can('update', $solution)
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSolutionModal{{ $solution->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                            @endunless

                            @if($userRole === 'admin' || $userRole === 'chef_dep' || Auth::user()->can('update', $solution))
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteSolutionModal{{ $solution->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        {!! nl2br(e($solution->content)) !!}
                    </div>

                    @if($solution->attachments && count($solution->attachments) > 0)
                        <div class="mb-3">
                            <h6 class="fw-bold">Attachments</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($solution->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="fas fa-file me-1"></i> {{ basename($attachment) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Comments -->
                    <div>
                        <h6 class="fw-bold">Comments ({{ $solution->comments->count() }})</h6>
                        @if($userRole !== 'admin' )
                        <form class="d-flex gap-2 align-items-center mb-3" action="{{ route('comments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="problem_id" value="{{ $problem->id }}">
                            <input type="hidden" name="solution_id" value="{{ $solution->id }}">
                            <input type="text" class="form-control" name="comment" placeholder="Write a comment..." required>
                            <button type="submit" class="btn btn-sm btn-primary">Post</button>
                        </form>
                        @endif

                        @foreach($solution->comments as $comment)
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $comment->user->profile_pic ? Storage::url($comment->user->profile_pic) : asset('frontend/assets/usericon.png') }}" class="rounded-circle me-2" width="30" height="30">
                                        <div>
                                            <div class="fw-semibold small">{{ $comment->user->name }}</div>
                                            <div class="text-muted small">{{ $comment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    @can('delete', $comment)
                                        <form method="POST" action="{{ route('comments.destroy', ['problem' => $problem->id, 'solution' => $solution->id, 'comment' => $comment->id]) }}" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                                <div class="mt-2 small">{{ $comment->comment }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-1"></i> No solutions yet. Be the first to contribute!
            </div>
        @endforelse
    </section>

    @if($solutions instanceof \Illuminate\Pagination\AbstractPaginator && $solutions->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $solutions->onEachSide(1)->links() }}
        </div>
    @endif

</main>

@endsection
