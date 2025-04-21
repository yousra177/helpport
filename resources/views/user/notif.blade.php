@extends('layouts.app')

@section('content')
<div class="container my-5">
    <section class="notification-section">
        <h2 class="mb-4 text-center">🔔 Notifications</h2>

        @auth
            @if(auth()->user()->notifications->count() > 0)
                @foreach(auth()->user()->notifications as $notification)
                    <div class="card mb-3 shadow-sm {{ $notification->read_at ? '' : 'border-success' }}">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">
                                    @if(isset($notification->data['title']))
                                        @if($notification->type === 'App\Notifications\ProblemCreatedNotification')
                                            📢 New Problem: {{ $notification->data['title'] }}
                                        @elseif($notification->type === 'App\Notifications\ProblemDeletedNotification')
                                            🗑️ Problem Deleted: {{ $notification->data['title'] }}
                                        @else
                                            ℹ️ {{ $notification->data['title'] ?? 'Notification' }}
                                        @endif
                                    @else
                                        ℹ️ System Notification
                                    @endif

                                    @if(!$notification->read_at)
                                        <span class="badge bg-success ms-2">New</span>
                                    @endif
                                </h5>
                                <p class="card-text text-muted mb-0">
                                    @if(isset($notification->data['message']))
                                        {{ $notification->data['message'] }}
                                    @endif
                                    <small class="text-muted d-block mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </p>
                            </div>

                            <div class="d-flex align-items-center">
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="btn btn-outline-primary btn-sm me-2">
                                        View
                                    </a>
                                @endif

                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success btn-sm">Mark as Read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5>No notifications yet</h5>
                        <p class="text-muted">We'll notify you when something new arrives.</p>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                    <h5>Please log in</h5>
                    <p class="text-muted">
                        <a href="{{ route('login') }}" class="btn btn-primary">Sign in</a> to view your notifications
                    </p>
                </div>
            </div>
        @endauth
    </section>
</div>
@endsection
