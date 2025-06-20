<!DOCTYPE html>
<html>
<head>
    <title>Sales Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .flex-grow-1 {
            flex-grow: 1;
            margin-right: 15px;
        }
        .export-btn-container {
            min-width: 120px;
        }
        /* Fix pagination arrows */
        .pagination .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Ensure export button stays visible */
        .export-btn-container {
            flex-shrink: 0;
        }

        /* Responsive filter form */
        @media (max-width: 768px) {
            .flex-grow-1 {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
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
        <h1 class="mb-4">Sales Management</h1>
        
        <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <form action="{{ route('admin.sales.index') }}" method="GET" class="flex-grow-1 me-3 mb-2 mb-md-0">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Dealer</label>
                                <select name="dealer_id" class="form-select">
                                    <option value="">All Dealers</option>
                                    @foreach($dealers as $dealer)
                                        <option value="{{ $dealer->id }}" {{ request('dealer_id') == $dealer->id ? 'selected' : '' }}>
                                            {{ $dealer->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Vehicle Model</label>
                                <input type="text" name="vehicle_model" class="form-control" value="{{ request('vehicle_model') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary ms-2">Reset</a>
                            </div>
                        </div>
                    </form>
                    
                    <div class="export-btn-container">
                    @if(request()->anyFilled(['dealer_id', 'vehicle_model', 'date']))
                        <a href="{{ route('admin.sales.export', request()->query()) }}" 
                        class="btn btn-success">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                @endif
            </div>
        </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Dealer</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Model</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>{{ $sale->date ? \Carbon\Carbon::parse($sale->date)->format('d/m/Y') : '' }}</td>
                                <td>{{ $sale->dealer_name }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td>{{ $sale->vehicle_number }}</td>
                                <td>{{ $sale->vehicle_model }}</td>
                                <td>Rs. {{ number_format($sale->grand_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>