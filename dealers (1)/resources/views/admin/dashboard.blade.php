<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.grns.index') ? 'active' : '' }}" href="{{ route('admin.grns.index') }}">GRNs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dealers.index') ? 'active' : '' }}" href="{{ route('admin.dealers.index') }}">Dealers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.parts.index') ? 'active' : '' }}" href="{{ route('admin.parts.index') }}">Parts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sales.index') ? 'active' : '' }}" href="{{ route('admin.sales.index') }}">Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">Login Sessions</a>
                </li>
            </ul>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </div>
</nav>

    <div class="container py-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Dealers</h5>
                        <p class="card-text display-4">{{ $dealerCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total GRNs</h5>
                        <p class="card-text display-4">{{ $grnCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Parts</h5>
                        <p class="card-text display-4">{{ $partCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>