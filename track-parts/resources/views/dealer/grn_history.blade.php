<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRN History</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #343a40;
        }
        table {
            margin-top: 20px;
        }
        .modal-lg {
            max-width: 900px;
        }
        #modal-items-body tr td {
            vertical-align: middle;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>GRN History</h2>
        
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>GRN Number</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Total Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grns as $index => $grn)
                <tr class="{{ $index % 2 == 0 ? 'table-danger' : '' }}">
                    <td>{{ $grn->grn_number }}</td>
                    <td>{{ $grn->invoice_number }}</td>
                    <td>{{ $grn->grn_date->format('d/m/Y') }}</td>
                    <td>{{ $grn->items->count() }}</td>
                    <td>
                    <button class="btn btn-sm btn-info view-grn" data-grn-id="{{ $grn->id }}">
                        <i class="fas fa-eye"></i> View
                    </button>
                        <a href="{{ route('grn.pdf', $grn->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            
        </table>
    </div>

   
    <div class="modal fade" id="grnDetailsModal" tabindex="-1" aria-labelledby="grnDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="grnDetailsModalLabel">GRN Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>GRN Number:</strong> <span id="modal-grn-number" class="text-primary"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Invoice Number:</strong> <span id="modal-invoice-number"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Date:</strong> <span id="modal-grn-date"></span>
                    </div>
                </div>
                
                <table class="table table-bordered table-striped">
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
                            <td colspan="2"><strong>Total Quantity</strong></td>
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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>