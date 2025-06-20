<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRN History</title>
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
            border-radius: 12px;
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
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .modal-content {
            border-radius: 8px;
            overflow: hidden;
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
        .rounded-table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        .rounded-table thead tr:first-child th:first-child {
            border-top-left-radius: 8px;
        }
        .rounded-table thead tr:first-child th:last-child {
            border-top-right-radius: 8px;
        }
        .rounded-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }
        .rounded-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }
        .rounded-table tbody tr:nth-of-type(odd) {
            background-color: #6188D0;
        }
        .rounded-table tbody tr:nth-of-type(even) {
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
    </style>
</head>
<body>
    <div class="position-relative">
        <div class="p-3 mb-2 bg-black">
            <button onclick="history.back()" class="btn btn-outline-light position-absolute top-0 start-0 m-3"> &#8249;</button>
            @if(file_exists(public_path('images/logo.webp')))
                <img src="{{ asset('images/logo.webp') }}" class="logo-img" alt="Company Logo">
            @endif
            <h1 class="font-semibold hidden md:block text-center">GRN History</h1>
        </div>
    </div>

    <div class="d-flex my-3 px-4 justify-content-end">
        <div class="input-group search-container">
            <input class="form-control me-2" type="search" placeholder="Search by GRN or Invoice number" aria-label="Search" id="searchInput" onkeyup="filterRecords()">
            <button class="btn search-btn" type="button" onclick="filterRecords()">Search</button>
        </div>
    </div>

    <div class="data-container">
        <div class="record-card">
            <div class="row fw-bold fs-5" style="color:#ED1D26;">
                <div class="col-md-3">GRN Number</div>
                <div class="col-md-3">Invoice Number</div>
                <div class="col-md-2">Date</div>
                <div class="col-md-2">Total Items</div>
                <div class="col-md-1">View</div>
                <div class="col-md-1">PDF</div>
            </div>
        </div>
        
        @foreach($grns as $grn)
        <div class="record-card" data-search="{{ strtolower($grn->grn_number) }} {{ strtolower($grn->invoice_number) }}">
            <div class="row">
                <div class="col-md-3">{{ $grn->grn_number }}</div>
                <div class="col-md-3">{{ $grn->invoice_number }}</div>
                <div class="col-md-2">{{ $grn->grn_date->format('d/m/Y') }}</div>
                <div class="col-md-2">{{ $grn->items->count() }}</div>
                <div class="col-md-1">
                    <button class="eye-btn btn-sm btn-outline-primary view-grn" data-grn-id="{{ $grn->id }}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('grn.pdf', $grn->id) }}" class="icon-btn" target="_blank">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="grnDetailsModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">GRN Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>GRN Number:</strong> <span class="text-primary" id="modal-grn-number"></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Invoice Number:</strong> <span class="text-primary" id="modal-invoice-number"></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Date:</strong> <span id="modal-grn-date"></span>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped rounded-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Part Number</th>
                                <th>Part Name</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-body">
                            <!-- Content will be added dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Total Quantity & Value</strong></td>
                                <td id="modal-total-qty"></td>
                                <td id="modal-total-value"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        function filterRecords() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const records = document.querySelectorAll('.record-card[data-search]');

            records.forEach(record => {
                const searchValue = record.getAttribute('data-search');
                record.style.display = searchValue.includes(input) ? '' : 'none';
            });
        }


    $(document).ready(function() {
        // Initialize Bootstrap modal
        const grnDetailsModal = new bootstrap.Modal(document.getElementById('grnDetailsModal'));
        
        $('.view-grn').click(function() {
            const grnId = $(this).data('grn-id');
            
            // Show loading state
            $('#modal-items-body').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
            grnDetailsModal.show();
            
            // Fetch GRN details
            $.get(`/grn/${grnId}/details`, function(data) {
                $('#modal-grn-number').text(data.grn_number);
                $('#modal-invoice-number').text(data.invoice_number);
                $('#modal-grn-date').text(data.grn_date);
                
                let itemsHtml = '';
                let totalQty = 0;
                let totalValue = 0;
                
                data.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.global_part.part_number}</td>
                            <td>${item.global_part.part_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.grn_unit_price}</td>
                        </tr>
                    `;
                    
                    totalQty += parseInt(item.quantity);
                    totalValue += parseFloat(item.quantity * item.grn_unit_price);
                });
                
                $('#modal-items-body').html(itemsHtml);
                $('#modal-total-qty').text(totalQty);
                $('#modal-total-value').text(totalValue.toFixed(2));
            }).fail(function() {
                $('#modal-items-body').html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
            });
        });
    });
</script>
</body>
</html>