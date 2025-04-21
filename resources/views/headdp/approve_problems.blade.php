<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Portal</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .logo { height: 50px; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; }
        .container-custom { max-width: 800px; margin: auto; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
        <div class="d-flex align-items-center">
            <img src="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" alt="Logo" class="logo me-3">
            <input type="text" class="form-control me-2" placeholder="Search Algérie Télécom">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
        </div>
        <div>
            <a href="{{ url('head/dashboard') }}" class="btn btn-outline-dark me-2"><i class="fas fa-home"></i></a>
            <a href="{{ url('head/create-user') }}" class="btn btn-outline-dark"><i class="fas fa-users"></i></a>
            <a href="{{ url('notification') }}" class="btn btn-outline-dark"><i class="fas fa-bell"></i></a>
            <a href="{{ url('head/approve-problems') }}" class="btn btn-outline-success"><i class="fas fa-check-circle"></i> Approve Problems</a>
        </div>
        @auth
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <img src="{{ asset('frontend/assets/usericon.png') }}" alt="Profile" class="profile-pic me-2">
                <span>{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </header>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="mb-4">Approve Problems</h2>

        @if ($problems->isEmpty())
            <p class="text-center text-muted">No problems pending approval.</p>
        @else
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($problems as $problem)
                        <tr>
                            <td>{{ $problem->title }}</td>
                            <td>{{ $problem->description }}</td>
                            <td>
                                <!-- Approve Button -->
                                <form method="POST" action="{{ route('head.problems.approve', $problem->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
                                </form>

                                <!-- Delete Button (Trigger Modal) -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $problem->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteModal{{ $problem->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this problem?</p>
                                                <form action="{{ route('head.approve_problems.destroy', $problem->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <label for="deleteReason">Reason for deletion:</label>
                                                    <textarea name="delete_reason" id="deleteReason" class="form-control" required></textarea>
                                                    <div class="mt-3">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script src="{{ asset('frontend/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
