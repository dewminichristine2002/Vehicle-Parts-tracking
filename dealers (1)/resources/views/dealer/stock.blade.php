<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Stock Overview</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #999999;
            color: #fff;
            font-family:'Nunito Sans', sans-serif;
        }
        .data-container {
            width: 95%;
            margin: 1rem auto;
        }
        .record-card {
            background-color: #000000;
            padding: 1rem 1.5rem;
            margin-bottom: 0.1rem;
            color:white;
        }

        .back-btn{
            color: white;
            background: none;
            border: none;
            font-size: 30px;
        }
        .icon-btn{
            color: #00cfd1;
            background: none;
            border: none;
        }
        .eye-btn {
            color: white;
            background: none;
            border:none;
        }
        .modal-header {
            font-family:'Nunito Sans', sans-serif;
            background-color: #213864;
            color: white;
        }
        .modal-body{
            color: black;
            font-family:'Nunito Sans', sans-serif;
        }
        .modal-footer .btn-danger {
            background-color: #ED1D26;
            border: none;
            padding: 8px 20px;
            font-weight: 600;
        }
        table tbody tr:nth-of-type(odd) {
            background-color: #6188D0;
        }
        table tbody tr:nth-of-type(even) {
            background-color: #BECADF;
        }
        .logo-img {
            height: 50px;
            position: absolute;
            top: 20px;
            left: 80px;
        }
        .search-container {
            max-width: 400px;
        }
        .search-btn {
            background-color: #ED1D26; 
            color: white;
        }

        body {
      background-color: #f8f9fa;
      height: 100%;
      overflow-y: auto;
      font-family: 'Nunito Sans', sans-serif;
    }
    .table-container {
      margin: 2rem auto;
      width: 95%;
      background-color: #1e1e1e;
      border-radius: 8px;
      padding: 1rem;
      color: white;
    }
    .search-filter {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    .pagination-custom .btn,
    .pagination-custom .form-control {
      background-color: #222;
      color: white;
      border: none;
      padding: 0.3rem 0.8rem;
      margin-left: 5px;
    }
    .pagination-custom .form-control {
      width: 50px;
      display: inline-block;
      text-align: center;
    }
    .low-stock {
      color: #ff0000 !important;
      font-weight: bold;
    }
    .normal-stock {
      color: #FBE947 !important;
    }
    .header-image {
      width: 100%;
      height: auto;
    }
    .logo-image {
      height: 50px;
      position: absolute;
      left: 80px; /* Increased from 20px to 80px */
      top: 20px;
    }
    .store-image {
      height: 50px;
      position: absolute;
      right: 20px;
      top: 20px;
    }
    .back-button {
      position: absolute;
      left: 20px;
      top: 20px;
      z-index: 10; /* Ensure button stays above other elements */
    }
    </style>
</head>
<body>
    <div class="position-relative">
        <div class="p-3 mb-2 bg-black">
            <button onclick="history.back()" class="btn btn-outline-light position-absolute top-0 start-0 m-3"> &#8249;</button>
            <img src="{{ asset('images/logo.webp') }}" class="logo-img" alt="Company Logo">
            
            <h1 class="font-semibold hidden md:block text-center">My Stock Overview</h1>
        </div>
    </div>
<body>


<div class="table-container">
  <div class="search-filter">
    <form action="{{ route('dealer.stock') }}" method="GET" class="w-100 d-flex justify-content-between">
      <input type="text" name="search" class="form-control w-50" placeholder="Search by part number or name" value="{{ request('search') }}">
      <select name="quantity_filter" class="form-select w-25">
        <option value="all" {{ request('quantity_filter') == 'all' ? 'selected' : '' }}>All Quantities</option>
        <option value="min" {{ request('quantity_filter') == 'min' ? 'selected' : '' }}>Less than 5 pcs</option>
        <option value="max" {{ request('quantity_filter') == 'max' ? 'selected' : '' }}>More than 10 pcs</option>
      </select>
      <button class="btn btn-primary ms-2" type="submit">Search</button>
      <a href="{{ route('dealer.stock') }}" class="btn btn-secondary ms-2">Reset</a>
    </form>
  </div>

  <table class="table table-dark table-hover">
    <thead>
      <tr>
        <th>Part Number</th>
        <th>Part Name</th>
        <th>Available Quantity</th>
      </tr>
    </thead>
    <tbody>
      @forelse($stocks as $stock)
        <tr>
          <td>{{ $stock->part_number }}</td>
          <td>{{ $stock->part_name }}</td>
          <td class="@if($stock->total_quantity <= 5) low-stock @else normal-stock @endif">
            {{ $stock->total_quantity }} pcs
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="text-center">No stock items found</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="pagination-custom d-flex justify-content-end align-items-center me-5">
  {{ $stocks->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>