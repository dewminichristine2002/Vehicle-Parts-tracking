<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dealer GRN</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --success: #4cc9f0;
            --danger: #f72585;
            --danger-dark: #e5177b;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border: #dee2e6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --radius: 8px;
            --radius-sm: 4px;
            --transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f7fb;
            padding: 20px;
        }

        .container {
            max-width: 12500px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        h2 {
            color: var(--primary);
            margin-bottom: 24px;
            font-weight: 600;
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #e8f4fd;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 14px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-control {
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            width: 30%;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 14px;
        }

        .part-select {
            width: 100%;
            min-width: 300px;
        }

        .part-row {
            margin-bottom: 15px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 16px;
            background: white;
            transition: var(--transition);
            overflow-x: auto;
        }

        .part-row:hover {
            border-color: var(--primary);
        }

        .part-row > div {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #3aa8d1;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: var(--danger-dark);
        }

      
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: var(--radius);
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow-md);
            transform: translateY(20px);
            transition: var(--transition);
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            margin-bottom: 16px;
        }

        .modal-header h3 {
            color: var(--primary);
            font-weight: 600;
        }

        .modal-body {
            margin-bottom: 24px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Confirmation Table */
        .confirmation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .confirmation-table th,
        .confirmation-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .confirmation-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: 500;
            min-width: 120px;
        }

        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }

        
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            background-color: var(--success);
            color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            z-index: 1100;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .notification.show {
            opacity: 1;
            visibility: visible;
        }

        .notification-icon {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .part-row {
                flex-direction: row;
                align-items: center;
            }
            
            .part-row > div {
                min-width: 120px;
            }
            
            .form-control {
                width: 100%;
            }
            }

            #parts-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            }

            .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            position: sticky;
            bottom: 20px;
            background: white;
            padding: 10px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            z-index: 100;
            }

            .part-row {
            transition: all 0.3s ease;
            }

            html {
            scroll-behavior: smooth;
            }
    </style>
</head>

<body>
    <div class="container">
        <h2>Goods Received Note (GRN)</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                <div>
                    <a href="{{ route('grn.history') }}" class="btn btn-sm btn-primary">View GRN History</a>
                    <a href="{{ route('grn.pdf', session('grn_id')) }}" class="btn btn-sm btn-secondary">Download PDF</a>
                </div>
            </div>
        @endif

        <form action="{{ route('grn.store') }}" method="POST" id="grn-form">
    @csrf
    <div class="form-row">
        <div class="form-group" style="flex: 1;">
            <label>GRN Date</label>
            <input type="date" name="grn_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        
        <div class="form-group" style="flex: 1;">
            <label>Invoice Number</label>
            <input type="text" name="invoice_number" class="form-control" required>
        </div>
    </div>

    <div id="parts-container">
    <div class="part-row">
        <div>
            <label>Part</label>
            <select name="parts[0][global_part_id]" class="part-select" required>
                <option value="">Select Part</option>
                @foreach($globalParts as $part)
                    <option value="{{ $part->id }}" data-part-number="{{ $part->part_number }}">
                        {{ $part->part_number }} - {{ $part->part_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Part Number</label>
            <input type="text" class="form-control part-number" readonly style="width: 120px;">
        </div>

        <div>
            <label>Available Qty</label>
            <input type="number" class="form-control available-quantity" value="0" readonly style="width: 80px;">
        </div>

        <div>
            <label>New Qty</label>
            <input type="number" name="parts[0][new_quantity]" min="1" class="form-control new-quantity" required style="width: 80px;">
        </div>

        <div>
            <label>Total Qty</label>
            <input type="number" class="form-control total-quantity" value="0" readonly style="width: 80px;">
        </div>

        <div>
            <label>GRN Unit Price</label>
            <input type="number" name="parts[0][grn_unit_price]" step="0.01" min="0" class="form-control grn-unit-price" required style="width: 100px;">
        </div>

        <button type="button" class="btn btn-danger remove-part">Remove</button>
    </div>
</div>
    <div class="action-buttons">
        <button type="button" id="add-part" class="btn btn-success">+ Add Part</button>
        <button type="submit" id="submit-grn" class="btn btn-primary">Save GRN</button>
    </div>
</form>
    </div>

   
    <div id="confirm-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm GRN Submission</h3>
            </div>
            <div class="modal-body" id="confirm-content">
                <div class="confirmation-details">
                    <div class="detail-row">
                        <span class="detail-label">GRN Date:</span>
                        <span class="detail-value" id="confirm-grn-date"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Invoice Number:</span>
                        <span class="detail-value" id="confirm-invoice-number"></span>
                    </div>
                    
                    <h4 style="margin: 16px 0 8px;">Parts Summary</h4>
                    <div class="table-responsive">
                    <table class="confirmation-table">
    <thead>
        <tr>
            <th>Part Name</th>
            <th>Part Number</th>
            <th>Quantity</th>
            <th>Unit Price</th>
        </tr>
    </thead>
    <tbody id="confirm-parts-body">
        <!-- Content will be added dynamically -->
    </tbody>
</table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancel-submit" class="btn btn-secondary">Cancel</button>
                <button id="confirm-submit" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>

    
    <div id="success-notification" class="notification">
        <span class="notification-icon">✓</span>
        <span>GRN submitted successfully!</span>
    </div>

    <script>
        $(document).ready(function() {
            
            $('.part-select').select2({
                placeholder: "Search for a part...",
                allowClear: true,
                width: 'resolve'
            });

          
            const localStocks = {
                @foreach($localStocks as $stock)
                    "{{ $stock->global_part_id }}": {{ $stock->quantity }},
                @endforeach
            };

        
            $('#add-part').click(function() {
    const newIndex = $('.part-row').length;
    const newRow = $(`
        <div class="part-row">
            <div>
                <label>Part</label>
                <select name="parts[${newIndex}][global_part_id]" class="part-select" required>
                    <option value="">Select Part</option>
                    @foreach($globalParts as $part)
                        <option value="{{ $part->id }}" data-part-number="{{ $part->part_number }}">
                            {{ $part->part_number }} - {{ $part->part_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Part Number</label>
                <input type="text" class="form-control part-number" readonly style="width: 120px;">
            </div>

            <div>
                <label>Available Qty</label>
                <input type="number" class="form-control available-quantity" value="0" readonly style="width: 80px;">
            </div>

            <div>
                <label>New Qty</label>
                <input type="number" name="parts[${newIndex}][new_quantity]" min="1" class="form-control new-quantity" required style="width: 80px;">
            </div>

            <div>
                <label>Total Qty</label>
                <input type="number" class="form-control total-quantity" value="0" readonly style="width: 80px;">
            </div>

            <div>
                <label>GRN Unit Price</label>
                <input type="number" name="parts[${newIndex}][grn_unit_price]" step="0.01" min="0" class="form-control grn-unit-price" required style="width: 100px;">
            </div>

            <button type="button" class="btn btn-danger remove-part">Remove</button>
        </div>
    `);

    $('#parts-container').append(newRow);
    newRow.find('.part-select').select2({
        placeholder: "Search for a part...",
        allowClear: true,
        width: 'resolve'
    });
    
    // Scroll to the new row if it's not visible
    newRow[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});

            
$(document).on('click', '.remove-part', function() {
    if ($('.part-row').length > 1) {
        $(this).closest('.part-row').remove();
        updateRowIndexes();
    } else {
        alert('At least one part must remain in the GRN.');
    }
});

            
            $(document).on('change', '.part-select', function() {
                const row = $(this).closest('.part-row');
                const partId = $(this).val();
                const selectedOption = $(this).find('option:selected');
                
                row.find('.part-number').val(selectedOption.data('part-number'));
                
                const availableQty = localStocks[partId] || 0;
                row.find('.available-quantity').val(availableQty);
                
                updateTotalQuantity(row);
                
                if (isPartDuplicate(partId)) {
                    alert('This part is already selected!');
                    $(this).val(null).trigger('change');
                    return;
                }
            });

            
            $(document).on('input', '.new-quantity', function() {
                updateTotalQuantity($(this).closest('.part-row'));
            });

            function updateTotalQuantity(row) {
                const available = parseInt(row.find('.available-quantity').val()) || 0;
                const newQty = parseInt(row.find('.new-quantity').val()) || 0;
                row.find('.total-quantity').val(available + newQty);
            }

            function isPartDuplicate(selectedPart) {
                if (!selectedPart) return false;
                let count = 0;
                $('.part-select').each(function() {
                    if ($(this).val() == selectedPart) count++;
                });
                return count > 1;
            }

         
            function updateRowIndexes() {
                $('.part-row').each(function(index) {
                    $(this).find('select').attr('name', `parts[${index}][global_part_id]`);
                    $(this).find('.new-quantity').attr('name', `parts[${index}][new_quantity]`);
                    $(this).find('.grn-unit-price').attr('name', `parts[${index}][grn_unit_price]`);
                });
            }
            
        
            $('#submit-grn').click(function(e) {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    $('#confirm-grn-date').text($('input[name="grn_date"]').val());
    $('#confirm-invoice-number').text($('input[name="invoice_number"]').val());
    
    $('#confirm-parts-body').empty();
    
    $('.part-row').each(function() {
        const partSelect = $(this).find('.part-select');
        const partText = partSelect.find('option:selected').text();
        const partNumber = $(this).find('.part-number').val();
        const quantity = $(this).find('.new-quantity').val();
        const unitPrice = $(this).find('.grn-unit-price').val();
        
        const partInfo = partText.split(' - ');
        const partName = partInfo.length > 1 ? partInfo[1] : partInfo[0];
        
        $('#confirm-parts-body').append(`
            <tr>
                <td>${partName}</td>
                <td>${partNumber}</td>
                <td>${quantity}</td>
                <td>${unitPrice}</td>
            </tr>
        `);
    });
    
    $('#confirm-modal').addClass('active');
});

            $('#confirm-submit').click(function() {
             
                if (!$('input[name="grn_number"]').length) {
                    const grnNumber = 'GRN-' + new Date().toISOString().slice(0,10).replace(/-/g, '') + 
                                    '-' + Math.floor(10000 + Math.random() * 90000);
                    $('#grn-form').append(`<input type="hidden" name="grn_number" value="${grnNumber}">`);
                }
                
              
                const notification = $('#success-notification');
                notification.addClass('show');
                
            
                setTimeout(() => {
                    notification.removeClass('show');
                }, 3000);
                
             
                $('#grn-form').submit();
            });

            $('#cancel-submit').click(function() {
                $('#confirm-modal').removeClass('active');
            });

           
            function validateForm() {
            let isValid = true;
            let hasAtLeastOneRow = false;

            $('.part-row').each(function() {
            hasAtLeastOneRow = true;
            const part = $(this).find('.part-select').val();
            const quantity = $(this).find('.new-quantity').val();
            const unitPrice = $(this).find('.grn-unit-price').val();

            if (!part || !quantity || quantity <= 0 || !unitPrice || unitPrice <= 0) {
            alert('Please fill all fields correctly.');
            isValid = false;
            return false;
        }
    });

    if (!hasAtLeastOneRow) {
        alert('Please add at least one part.');
        return false;
    }

    if (!$('input[name="invoice_number"]').val()) {
        alert('Please enter an invoice number.');
        return false;
    }

    return isValid && hasAtLeastOneRow;
}
        });
    </script>
</body>
</html>