<!DOCTYPE html>
<html>
<head>
    <title>Dealer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
        }
        .fixed-column {
            position: sticky;
            right: 0;
            background-color: white;
            z-index: 1;
        }
        .status-col {
            position: sticky;
            right: 220px;
            background-color: white;
            z-index: 1;
        }
        .table th.fixed-column,
        .table td.fixed-column {
            min-width: 300px; /* Increased width to accommodate new button */
            max-width: 300px;
        }
        .table th.status-col,
        .table td.status-col {
            min-width: 120px;
            max-width: 120px;
        }
        .badge-active {
            background-color: #28a745;
        }
        .badge-inactive {
            background-color: #6c757d;
        }
        .badge-suspended {
            background-color: #dc3545;
        }
        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
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
        <h1 class="mb-4">Dealer Management</h1>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('admin.dealers.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search dealers..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                                @if(request('search'))
                                    <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Login Email</th>
                                <th>Company Email</th>
                                <th>Company Mobile</th>
                                <th>Owner Mobile</th>
                                <th>User Contact</th>
                                <th>GRNs</th>
                                <th>Stock Items</th>
                                <th>Registered</th>
                                <th>Last Login</th>
                                <th class="status-col">Status</th>
                                <th class="fixed-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dealers as $dealer)
                            <tr>
                                <td>{{ $dealer->id }}</td>
                                <td>
                                    @if($dealer->company_logo)
                                        <img src="{{ asset($dealer->company_logo) }}" class="logo-img">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                            <i class="bi bi-building text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $dealer->name }}</td>
                                <td>{{ $dealer->company_name }}</td>
                                <td>{{ $dealer->email }}</td>
                                <td>{{ $dealer->company_email }}</td>
                                <td>{{ $dealer->company_mobile }}</td>
                                <td>{{ $dealer->owner_mobile }}</td>
                                <td>{{ $dealer->user_contact }}</td>
                                <td>{{ $dealer->grns_count }}</td>
                                <td>{{ $dealer->local_stocks_count }}</td>
                                <td>
                                    @if($dealer->registered_at)
                                        {{ $dealer->registered_at->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($dealer->last_login_at)
                                        {{ $dealer->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        Never
                                    @endif
                                </td>
                                <td class="status-col">
                                    @if($dealer->is_active)
                                        <span class="badge badge-active">Active</span>
                                    @else
                                        <span class="badge badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="fixed-column">
                                    <div class="action-buttons">
                                        <form action="{{ route('admin.dealers.toggle-status', $dealer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-sm {{ $dealer->is_active ? 'btn-warning' : 'btn-success' }}">
                                                <i class="bi bi-power"></i> {{ $dealer->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-primary edit-dealer" data-bs-toggle="modal" 
                                            data-bs-target="#editDealerModal" data-dealer-id="{{ $dealer->id }}"
                                            data-name="{{ $dealer->name }}"
                                            data-company="{{ $dealer->company_name }}"
                                            data-email="{{ $dealer->email }}"
                                            data-company-address="{{ $dealer->company_address }}"
                                            data-company-mobile="{{ $dealer->company_mobile }}"
                                            data-company-email="{{ $dealer->company_email }}"
                                            data-owner-mobile="{{ $dealer->owner_mobile }}"
                                            data-user-name="{{ $dealer->user_name }}"
                                            data-user-designation="{{ $dealer->user_designation }}"
                                            data-user-email="{{ $dealer->user_email }}"
                                            data-user-contact="{{ $dealer->user_contact }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info view-dealer" data-bs-toggle="modal" 
                                            data-bs-target="#viewDealerModal" data-dealer-id="{{ $dealer->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form action="{{ route('admin.dealers.destroy', $dealer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $dealers->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Dealer Modal -->
    <div class="modal fade" id="editDealerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dealer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDealerForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name*</label>
                                    <input type="text" class="form-control" name="name" id="editName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name*</label>
                                    <input type="text" class="form-control" name="company_name" id="editCompany" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Login Email*</label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Email*</label>
                                    <input type="email" class="form-control" name="company_email" id="editCompanyEmail" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Mobile*</label>
                                    <input type="text" class="form-control" name="company_mobile" id="editCompanyMobile" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Owner Mobile</label>
                                    <input type="text" class="form-control" name="owner_mobile" id="editOwnerMobile">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Company Address</label>
                            <textarea class="form-control" name="company_address" id="editCompanyAddress" rows="2"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" class="form-control" name="user_name" id="editUserName">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User Designation</label>
                                    <input type="text" class="form-control" name="user_designation" id="editUserDesignation">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User Email</label>
                                    <input type="email" class="form-control" name="user_email" id="editUserEmail">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User Contact*</label>
                                    <input type="text" class="form-control" name="user_contact" id="editUserContact" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Company Logo</label>
                            <input type="file" class="form-control" name="company_logo">
                            <small class="text-muted">Leave empty to keep current logo</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Dealer Modal -->
    <div class="modal fade" id="viewDealerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dealer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div id="viewLogoContainer" class="mb-2">
                                <!-- Logo will be inserted here by JS -->
                            </div>
                            <div>
                                <span class="badge" id="viewStatusBadge">Status</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <span id="viewName"></span></p>
                                    <p><strong>Company:</strong> <span id="viewCompany"></span></p>
                                    <p><strong>Login Email:</strong> <span id="viewEmail"></span></p>
                                    <p><strong>Company Email:</strong> <span id="viewCompanyEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Registered:</strong> <span id="viewRegistered"></span></p>
                                    <p><strong>Last Login:</strong> <span id="viewLastLogin"></span></p>
                                    <p><strong>GRNs:</strong> <span id="viewGrnsCount"></span></p>
                                    <p><strong>Stock Items:</strong> <span id="viewStocksCount"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Company Details</h6>
                            <p><strong>Address:</strong> <span id="viewCompanyAddress"></span></p>
                            <p><strong>Mobile:</strong> <span id="viewCompanyMobile"></span></p>
                            <p><strong>Owner Mobile:</strong> <span id="viewOwnerMobile"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>User Details</h6>
                            <p><strong>Name:</strong> <span id="viewUserName"></span></p>
                            <p><strong>Designation:</strong> <span id="viewUserDesignation"></span></p>
                            <p><strong>Email:</strong> <span id="viewUserEmail"></span></p>
                            <p><strong>Contact:</strong> <span id="viewUserContact"></span></p>
                        </div>
                    </div>
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
        // Edit dealer modal
        $('.edit-dealer').click(function() {
            const dealerId = $(this).data('dealer-id');
            const name = $(this).data('name');
            const company = $(this).data('company');
            const email = $(this).data('email');
            const companyAddress = $(this).data('company-address');
            const companyMobile = $(this).data('company-mobile');
            const companyEmail = $(this).data('company-email');
            const ownerMobile = $(this).data('owner-mobile');
            const userName = $(this).data('user-name');
            const userDesignation = $(this).data('user-designation');
            const userEmail = $(this).data('user-email');
            const userContact = $(this).data('user-contact');
            
            $('#editName').val(name);
            $('#editCompany').val(company);
            $('#editEmail').val(email);
            $('#editCompanyAddress').val(companyAddress);
            $('#editCompanyMobile').val(companyMobile);
            $('#editCompanyEmail').val(companyEmail);
            $('#editOwnerMobile').val(ownerMobile);
            $('#editUserName').val(userName);
            $('#editUserDesignation').val(userDesignation);
            $('#editUserEmail').val(userEmail);
            $('#editUserContact').val(userContact);
            
            $('#editDealerForm').attr('action', `/admin/dealers/${dealerId}`);
        });

        // View dealer modal
        $('.view-dealer').click(function() {
            const dealerId = $(this).data('dealer-id');
            const row = $(this).closest('tr');
            
            // Basic info
            $('#viewName').text(row.find('td').eq(2).text());
            $('#viewCompany').text(row.find('td').eq(3).text());
            $('#viewEmail').text(row.find('td').eq(4).text());
            $('#viewCompanyEmail').text(row.find('td').eq(5).text());
            $('#viewCompanyMobile').text(row.find('td').eq(6).text());
            $('#viewOwnerMobile').text(row.find('td').eq(7).text());
            $('#viewUserContact').text(row.find('td').eq(8).text());
            $('#viewGrnsCount').text(row.find('td').eq(9).text());
            $('#viewStocksCount').text(row.find('td').eq(10).text());
            $('#viewRegistered').text(row.find('td').eq(11).text());
            $('#viewLastLogin').text(row.find('td').eq(12).text());
            
            // Status
            const statusBadge = row.find('.status-col .badge').clone();
            $('#viewStatusBadge').replaceWith(statusBadge);
            
            // Logo
            const logoContainer = $('#viewLogoContainer');
            logoContainer.empty();
            const logo = row.find('td').eq(1).find('img, div').clone();
            logoContainer.append(logo);
            
            // Additional details (from data attributes)
            const dealerData = $(this).data();
            $('#viewCompanyAddress').text(dealerData.companyAddress || 'N/A');
            $('#viewUserName').text(dealerData.userName || 'N/A');
            $('#viewUserDesignation').text(dealerData.userDesignation || 'N/A');
            $('#viewUserEmail').text(dealerData.userEmail || 'N/A');
        });
    });
    </script>
</body>
</html>