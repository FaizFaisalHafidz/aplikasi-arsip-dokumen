<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg">
    <div class="container-fluid">
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li class="nav-item topbar-user dropdown hidden-caret">
                {{-- User Profile --}}
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        <x-avatar></x-avatar>
                    </div>
                    <span class="profile-username">
                        <span class="op-7">Hi,</span> <span class="fw-bold">{{ auth()->user()->nama_user }}</span>
                    </span>
                </a>
                {{-- User Menu --}}
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    <x-avatar></x-avatar>
                                </div>
                                <div class="u-text">
                                    <h4>{{ auth()->user()->nama_user }}</h4>
                                    <p class="text-muted">{{ auth()->user()->role }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>

                            <a href="{{ route('password.edit') }}" class="dropdown-item py-2">
                                <i class="ti ti-lock fs-5 me-2"></i> Ubah Password
                            </a>

                            <div class="dropdown-divider mb-1"></div>
                            
                            <div class="dropdown-item py-3">
                                {{-- button logout --}}
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalLogout"> 
                                    Logout
                                </button>
                            </div>
                        </li>
                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>