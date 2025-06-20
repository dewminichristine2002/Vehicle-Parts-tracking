<!DOCTYPE html>
<html>
<head>
    <title>GRN Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h1 class="mb-4">GRN Management</h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('admin.grns.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by Dealer ID..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>GRN Number</th>
                                <th>Dealer</th>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grns as $grn)
                            <tr>
                                <td>{{ $grn->grn_number }}</td>
                                <td>{{ $grn->dealer->company_name }} </td>
                                <td>{{ $grn->invoice_number }}</td>
                                <td>{{ $grn->grn_date->format('d/m/Y') }}</td>
                                <td>{{ $grn->items->count() }}</td>
                                <td>
                                    <a href="{{ route('grn.pdf', $grn->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                    <button class="btn btn-sm btn-info view-grn" data-grn-id="{{ $grn->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $grns->links() }}
            </div>
        </div>
    </div>

   
    <div class="modal fade" id="grnDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">GRN Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="grnDetailsContent">
                 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.view-grn').click(function() {
            const grnId = $(this).data('grn-id');
            $('#grnDetailsContent').html('<div class="text-center my-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            $.get(`/grn/${grnId}/details`, function(data) {
                let itemsHtml = '';
                data.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.global_part.part_number}</td>
                            <td>${item.global_part.part_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.global_part.price}</td>
                        </tr>
                    `;
                });
                
                const html = `
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>GRN Number:</strong> ${data.grn_number}</div>
                        <div class="col-md-4"><strong>Invoice Number:</strong> ${data.invoice_number}</div>
                        <div class="col-md-4"><strong>Date:</strong> ${data.grn_date}</div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Part Number</th>
                                <th>Part Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                `;
                
                $('#grnDetailsContent').html(html);
            }).fail(function() {
                $('#grnDetailsContent').html('<div class="alert alert-danger">Failed to load GRN details</div>');
            });
            
            $('#grnDetailsModal').modal('show');
        });
    });
    </script>
</body>
</html>