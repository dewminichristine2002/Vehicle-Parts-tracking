<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monthly Targets</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background-color: #888;
    }
    h2 {
        color: white;
        text-align: center;
        margin-bottom: 20px;
    }
    .filter-form {
        display: flex;
        justify-content: right;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .filter-form input[type="text"],
    .filter-form input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
    }
    .filter-form button {
        padding: 8px 12px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 4px;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    background-color: #000;
    color: rgb(255, 255, 255);
    border: 1px solid #555;
}
    table th {
    background-color: #000;
    color: red; /* Header text color */
    padding: 10px;
    border-right: 1px solid #444;
    border-bottom: 2px solid rgb(255, 255, 255); /* Underline for the header row */
}

table td {
    color: white; /* Data text color */
    padding: 10px;
    border-right: 1px solid #444;
    border-bottom: 1px solid #555;
}


table th:last-child,
table td:last-child {
    border-right: none;
}


/* Header underline */
table thead tr {
    border-bottom: 2px solid red; /* strong underline for header row */
}

table tbody tr:not(:last-child) {
    border-bottom: 1px solid #555; /* row separation for data rows */
}

    .icon-btn {
        color: cyan;
        font-size: 18px;
        text-decoration: none;
    }
    
</style>
</head>
<body>
<div class="position-relative">
    <div class="d-flex align-items-center justify-content-center p-3 mb-2 bg-black position-relative">
    <!-- Back Button aligned vertically center -->
     <a href="{{ url('/dealer/dashboard') }}" class="btn btn-outline-light position-absolute start-0 ms-3">
        &#8249;
    </a>

    <!-- Logo beside the button (optional) -->
    <img src="{{ asset('images/logo.png') }}" class="position-absolute start-0 ms-5" style="height: 50px; left: 50px;"/>

    <!-- Centered Title -->
    <h1 class="text-center text-white w-100">Monthly Targets</h1>
</div>

  </div>

<!-- Bootstrap CSS (already included if you're using Laravel UI) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


@section('content')
<div class="container">

    <!-- Add Target Button -->
    <div class="d-flex justify-content-end">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTargetModal">Add New Target</button>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Target Amount</th>
                <th>Achieved Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($targets as $target)
                <tr>
                    <td>{{ $target->year }}</td>
                    <td>{{ $target->month_name }}</td>
                    <td>{{ number_format($target->target_amount, 2) }}</td>
                    <td>{{ number_format($target->achieved_amount, 2) }}</td>
                    <td>
                        <!-- Edit Button -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTargetModal{{ $target->id }}">Edit</button>

                        <!-- Delete Form -->
                        <form action="{{ route('targets.destroy', $target) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editTargetModal{{ $target->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('targets.update', $target) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Target</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('targets.partials.form', ['target' => $target])
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addTargetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('targets.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('targets.partials.form', ['target' => null])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>