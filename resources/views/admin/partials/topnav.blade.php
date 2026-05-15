<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="{{ $adminHomeUrl }}">Vibe Admin</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button"><i class="fas fa-bars"></i></button>
    <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
    </div>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/">Home</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <form method="post" action="{{ route('admin.logout') }}" class="px-3 py-1">
                        @csrf
                        <button type="submit" class="dropdown-item p-0 border-0 bg-transparent text-start">Logout</button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
