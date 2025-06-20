<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .detail-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .detail-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .detail-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-label {
            font-weight: 500;
            color: #6c757d;
        }
        .detail-value {
            font-weight: 500;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="detail-container">
            <div class="detail-header clearfix">
                <h2 class="float-start"><i class="bi bi-person-badge"></i> Dealer Details</h2>
                <a href="{{ route('admin.index') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="detail-card">
                        <h4><i class="bi bi-person-lines-fill"></i> Basic Information</h4>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">ID:</div>
                            <div class="col-8 detail-value">{{ $dealer->id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Name:</div>
                            <div class="col-8 detail-value">{{ $dealer->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Company:</div>
                            <div class="col-8 detail-value">{{ $dealer->company_name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Email:</div>
                            <div class="col-8 detail-value">{{ $dealer->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-card">
                        <h4><i class="bi bi-clock-history"></i> Activity Information</h4>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Registered:</div>
                            <div class="col-8 detail-value">
                                {{ $dealer->registered_at->format('M d, Y h:i A') }}
                                <br>
                                <small>({{ $dealer->registered_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Last Login:</div>
                            <div class="col-8 detail-value">
                                @if($dealer->last_login_at)
                                    {{ $dealer->last_login_at->format('M d, Y h:i A') }}
                                    <br>
                                    <small>({{ $dealer->last_login_at->diffForHumans() }})</small>
                                @else
                                    Never logged in
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 detail-label">Status:</div>
                            <div class="col-8 detail-value">
                                @if($dealer->last_login_at && $dealer->last_login_at->diffInDays() < 30)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Dealer account was created {{ $dealer->registered_at->diffForHumans() }}.
                    @if($dealer->last_login_at)
                        Last activity was {{ $dealer->last_login_at->diffForHumans() }}.
                    @else
                        This dealer has never logged in.
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>