<header class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
  <div class="d-flex align-items-center">
    <img src="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" alt="Algérie Télécom Logo" class="logo me-3" style="height: 50px;">

    <form method="GET" action="{{ route('problems.search') }}" class="d-flex">
  <input type="text" class="form-control me-2"
         placeholder="Search Algérie Télécom"
         name="search"
         value="{{ request('search') }}">
  <button class="btn btn-primary"><i class="fas fa-search"></i></button>
</form>
  </div>

  <div class="d-flex align-items-center gap-2">
    {{-- Dashboard is common --}}
    <a href="{{ url(Auth::user()->role === 'admin' ? 'admin' : (Auth::user()->role === 'chef_dep' ? 'head/dashboard' : 'dashboard')) }}"
       class="btn btn-outline-dark" title="Dashboard">
      <i class="fas fa-home"></i>
    </a>

    {{-- Role-based buttons --}}
    @if(Auth::user()->role === 'admin')
      <a href="{{ url('admin/create-user') }}" class="btn btn-outline-dark" title="Users">
        <i class="fas fa-users"></i>
      </a>
    @elseif(Auth::user()->role === 'chef_dep')
      <a href="{{ url('head/create-user') }}" class="btn btn-outline-dark" title="Users">
        <i class="fas fa-users"></i>
      </a>
      <a href="{{ url('head/approve-problems') }}" class="btn btn-outline-success" title="Approve">
        <i class="fas fa-check-circle"></i>
      </a>
    @endif

    {{-- Notifications are common --}}
    <a href="{{ url('notification') }}" class="btn btn-outline-dark position-relative" title="Notifications">
      <i class="fas fa-bell"></i>
      @if(auth()->user()->unreadNotifications->count() > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          {{ auth()->user()->unreadNotifications->count() }}
        </span>
      @endif
    </a>
  </div>

  @auth
  <div class="dropdown">
    <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
      <img src="{{ asset('frontend/assets/usericon.png') }}" alt="Profile" class="rounded-circle me-2" style="width: 40px; height: 40px;">
      <div class="text-start">
        <strong class="d-block">{{ Auth::user()->name }}</strong>
        <small class="text-muted">{{ Auth::user()->departement }}</small>
      </div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a href="{{ url('profile') }}" class="dropdown-item"><i class="fas fa-user me-2"></i> Edit Profile</a></li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
        </form>
      </li>
    </ul>
  </div>
  @endauth
</header>
