<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <style>
        body {
            background-color: #000000;
            font-family: 'Nunito Sans', sans-serif;
        }
        .card {
            background: #F5F5F5;
            border-radius: 8px;
            margin-top: 100px;
            width:93%;
            margin:100px auto 1rem auto;
        }
        .blurred {
            opacity: 0.3;
        }
        .btn-submit {
            background-color: #ED1D26;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            width: 120px;
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease-in-out;
        }
        .btn-add {
            color: green;
            background-color: aliceblue;
            border: 4px;
            border-radius: 20px;
            border-color: #126F26;
        }
        .btn-submit:hover, .btn-add:hover {
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }
        .success-popup {
            width: 50vw;
            position: fixed;
            left: 60%;
            transform: translateX(-50%);
            border-radius: 10px;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: none;
            z-index: 1050;
            transition: top 0.5s ease-in-out;
        }
        .part-select {
            width: 100%;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
    @if(session('success'))
        <div class="success-popup text-center" id="successPopup">
            <div class="alert alert-success position-absolute" role="alert">
               <h4 class="alert-heading">Well done!</h4>
               <p>{{ session('success') }}</p>
               <span class="text-success fs-4">✓</span>
            </div>
        </div>
    @endif

    <div class="container my-5" id="mainContent">
        <div class="align-items-center mb-3">
            <button onclick="history.back()" class="btn btn-outline-light position-absolute top-0 start-0 m-3"> &#8249; </button>
            <a href="https://dealers.idealgrouplk.com/dealer/dashboard">
            <img src="{{ asset('images/logo.webp') }}" class="position-absolute top-0 start-0 mt-3 ms-5 mb-5" style="height: 50px;" />
            </a>
        </div>
    </div>

    <form action="{{ route('grn.store') }}" method="POST" id="grn-form" class="card d-flex p-4">
        @csrf
        <div class="d-flex justify-content-between mb-3">
            <input type="text" name="invoice_number" class="form-control w-25" placeholder="Invoice number" required>
            <input type="date" name="grn_date" class="form-control w-25" value="{{ date('Y-m-d') }}" required>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th>Part Number</th>
                    <th>Available Quantity</th>
                    <th>GRN Quantity</th>
                    <th>Total Quantity</th>
                    <th>Unit Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td>
                        <select name="parts[0][global_part_id]" class="part-select" required>
                            <option value="">Select Part</option>
                            @foreach($globalParts as $part)
                                <option value="{{ $part->id }}" data-part-number="{{ $part->part_number }}">
                                    {{ $part->part_number }} - {{ $part->part_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" class="form-control part-number" readonly></td>
                    <td><input type="number" class="form-control available-quantity" value="0" readonly></td>
                    <td><input type="number" name="parts[0][new_quantity]" class="form-control new-quantity" min="1" required></td>
                    <td><input type="number" class="form-control total-quantity" value="0" readonly></td>
                    <td><input type="number" name="parts[0][grn_unit_price]" class="form-control grn-unit-price" step="0.01" min="0" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm" onclick="removeRow(this)">
                            <img src="{{ asset('images/delete.png') }}" alt="Delete">
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex">
            <button type="button" class="btn btn-add" onclick="addNewRow()">+ Add Item</button>
        </div>

        <div class="text-end mt-5">
            <button type="button" class="btn btn-submit" onclick="showGRN()">Submit</button>
        </div>
    </form>

    <div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
          <div class="modal-content p-4" style="border-radius: 15px;">
            <div class="modal-body" id="grnPreview"></div>
          </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const localStocks = {
            @foreach($localStocks as $stock)
                "{{ $stock->global_part_id }}": {{ $stock->quantity }},
            @endforeach
        };

        $(document).ready(function() {
            $('.part-select').select2({
                placeholder: "Search for a part...",
                allowClear: true,
                width: '100%'
            });

            @if(session('success'))
                showSuccessPopup();
            @endif
        });

        function addNewRow() {
            const rowCount = $('#tableBody tr').length;
            const newRow = `
                <tr>
                    <td>
                        <select name="parts[${rowCount}][global_part_id]" class="form-control part-select" required>
                            <option value="">Select Part</option>
                            @foreach($globalParts as $part)
    <option value="{{ $part->id }}" data-part-number="{{ $part->part_number }}" data-part-name="{{ $part->part_name }}">
        {{ $part->part_number }} - {{ $part->part_name }}
    </option>
@endforeach

                        </select>
                    </td>
                    <td><input type="text" class="form-control part-number" readonly></td>
                    <td><input type="number" class="form-control available-quantity" value="0" readonly></td>
                    <td><input type="number" name="parts[${rowCount}][new_quantity]" class="form-control new-quantity" min="1" required></td>
                    <td><input type="number" class="form-control total-quantity" value="0" readonly></td>
                    <td><input type="number" name="parts[${rowCount}][grn_unit_price]" class="form-control grn-unit-price" step="0.01" min="0" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm" onclick="removeRow(this)">
                            <img src="{{ asset('images/delete.png') }}" alt="Delete">
                        </button>
                    </td>
                </tr>
            `;
            $('#tableBody').append(newRow);
            $('.part-select').last().select2({
                placeholder: "Search for a part...",
                allowClear: true,
                width: '100%'
            });
        }

        function removeRow(button) {
            if ($('#tableBody tr').length > 1) {
                $(button).closest('tr').remove();
            } else {
                alert('At least one part must remain in the GRN.');
            }
        }

        $(document).on('change', '.part-select', function() {
            const row = $(this).closest('tr');
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
            updateTotalQuantity($(this).closest('tr'));
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

        function showGRN() {
            if (!validateForm()) return;
            
            const invoiceNumber = $('input[name="invoice_number"]').val();
            const grnDate = $('input[name="grn_date"]').val();
            
            let tableRows = '';
            $('.part-select').each(function() {
                const row = $(this).closest('tr');
                const partName = $(this).find('option:selected').text();
                const partNumber = row.find('.part-number').val();
                const quantity = row.find('.new-quantity').val();
                const unitPrice = row.find('.grn-unit-price').val();
                
                tableRows += `
                    <tr>
                        <td>${partName}</td>
                        <td>${partNumber}</td>
                        <td>${quantity}</td>
                        <td>${unitPrice}</td>
                    </tr>
                `;
            });

            const modalHTML = `
                <h4 class="fw-bold text-primary mb-3">Confirm GRN Submission</h4>
                <p><strong>GRN Date:</strong> ${grnDate}</p>
                <p><strong>Invoice Number:</strong> ${invoiceNumber}</p>
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Part Name</th>
                            <th>Part Number</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
                <div class="text-end">
                    <button type="button" class="btn btn-danger me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmGRN()">Confirm</button>
                </div>
            `;

            $('#grnPreview').html(modalHTML);
            new bootstrap.Modal(document.getElementById('grnModal')).show();
        }

        function confirmGRN() {
            $('#grn-form').submit();
        }

        function validateForm() {
            let isValid = true;
            let hasAtLeastOneRow = false;

            $('#tableBody tr').each(function() {
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

        function showSuccessPopup() {
            let popup = document.getElementById('successPopup');
            let body = document.body;
            let content = document.getElementById('mainContent');
            
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.top = '20px';
            }, 10);

            setTimeout(() => {
                popup.style.top = '-100px';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 500);
            }, 2000);
        }
    </script>
</body>
</html>