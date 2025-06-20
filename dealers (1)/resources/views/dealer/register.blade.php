<!DOCTYPE html>
<html>
<head>
    <title>Dealer Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .section-title {
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center mb-4">Dealer Registration</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('dealer.register') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <h5 class="section-title">Company Information</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name*</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Email*</label>
                        <input type="email" name="company_email" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Mobile*</label>
                        <input type="text" name="company_mobile" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Owner Mobile</label>
                        <input type="text" name="owner_mobile" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Company Address</label>
                    <textarea name="company_address" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Company Logo</label>
                    <input type="file" name="company_logo" class="form-control">
                </div>
            </div>

            <div class="form-section">
                <h5 class="section-title">User Information</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">User Name*</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Email (FOR LOGIN)*</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">User Designation</label>
                        <input type="text" name="user_designation" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Contact Number*</label>
                        <input type="text" name="user_contact" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Password*</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password*</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>

        <div class="mt-3 text-center">
            <p>Already registered? <a href="{{ route('dealer.login.form') }}">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>