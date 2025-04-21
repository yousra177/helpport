<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solution</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .solutions {
            margin-top: 20px;
        }
        .solution {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .comments {
            margin-top: 15px;
        }
        .add-comment {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .comment {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .file-input-label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
    <div class="d-flex align-items-center">
        <img src="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" alt="Algérie Télécom Logo" class="logo me-3">
        <div class="search-container d-flex">
            <input type="text" class="form-control me-2" placeholder="Search Algérie Télécom">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <div>
        <a href="{{ url('dashboard') }}" class="btn btn-outline-dark me-2"><i class="fas fa-home"></i></a>
    </div>

    @auth
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <div class="profile-container d-flex align-items-center">
                    <img src="{{ asset('frontend/assets/usericon.png') }}" alt="Profile" class="profile-pic me-2">
                    <div>
                        <span class="username">{{ Auth::user()->name }}</span><br>
                        <small class="role text-muted">{{ Auth::user()->departement }}</small>
                    </div>
                </div>
            </button>
            <ul class="dropdown-menu">
                <li><a href="{{ url('profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Edit Profile</a></li>
                <li><a href="{{ url('problem-saved') }}" class="dropdown-item"><i class="fas fa-tools"></i> Problem Saved</a></li>
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

<!-- Edit Solution Modal -->
    <div class="modal-dialog">
        <form method="POST" action="{{ route('solutions.update', ['problem' => $problem->id, 'solution' => $solution->id]) }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editSolutionModalLabel{{ $solution->id }}">Edit Solution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="solutionContent" class="form-label">Solution Content</label>
                    <textarea name="content" id="solutionContent" class="form-control" rows="4" required>{{ $solution->content }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Update Attachments:</label>
                    <input type="file" name="solution_attachments[]" class="form-control" id="solutionAttachments" accept="image/*,.pdf,.docx,.zip" multiple>
                    <div class="form-text">Leave blank to keep current attachments.</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Update Solution</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('frontend/js/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
