@extends('layouts.app')

@if(session('error'))
    <div style="background-color: #ffcccc; color: #a94442; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        ❗ {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">
        ✅ {{ session('success') }}
    </div>
@endif

@section('content')
<h2>Create Invoice</h2>

<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    <input type="hidden" name="invoice_no" id="invoice_no_hidden">

    <input type="text" name="customer_name" id="customer_name" placeholder="Customer Name" required>
    <input type="date" name="date" id="date" required>

    <h4>Sold Parts</h4>
    <table id="parts-table">
        <thead>
            <tr>
                <th>Part Number</th>
                <th>Part Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount (%)</th>
                <th>Discount Amount</th> <!-- NEW -->
                <th>Total</th>

                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <button type="button" onclick="addPartRow()">+ Add Part</button>

    <h4>Other Costs</h4>
    <table id="costs-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Price</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <button type="button" onclick="addCostRow()">+ Add Cost</button>

    <br><br>
    <label>Grand Total:</label>
    <input type="number" step="0.01" name="grand_total" id="grand_total" readonly>

    <br><br>
    <button type="button" onclick="showConfirmation()">Submit Invoice</button>
</form>

<!-- Confirmation Popup -->
<div id="confirmation-popup" style="display:none; position:fixed; top:10%; left:15%; width:70%; background:#fff; border:2px solid #000; padding:20px; z-index:9999;">
    <h3>Confirm Invoice Details</h3>
    <div id="confirmation-content"></div>
    <br>
    <button onclick="hidePopup()">Back</button>
    <button onclick="downloadConfirmation()">Download</button>
    <button onclick="document.getElementById('invoice-form').submit();">Confirm</button>
</div>

<script>
    const partsData = @json($parts);
    const vehicleParts = {};
    @foreach (\App\Models\VehiclePart::all() as $vp)
        vehicleParts["{{ $vp->part_number }}"] = {
            name: "{{ $vp->part_name }}",
            price: {{ $vp->unit_price }}
        };
    @endforeach

    let partIndex = 0;
    let costIndex = 0;
    const generatedInvoiceNo = 'INV' + Math.floor(100000 + Math.random() * 900000);
    document.getElementById('invoice_no_hidden').value = generatedInvoiceNo;

    function addPartRow() {
        const table = document.querySelector('#parts-table tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><input list="partNumbers" class="part-number-input" name="parts[${partIndex}][part_number]" onchange="syncFromPartNumber(this)" required></td>
            <td><input list="partNames" class="part-name-input" name="parts[${partIndex}][part_name]" onchange="syncFromPartName(this)" required></td>
            <td><input type="number" name="parts[${partIndex}][quantity]" min="1" value="1" onchange="calculateTotal(this)" required></td>
            <td><input type="number" name="parts[${partIndex}][unit_price]" step="0.01" readonly></td>
            <td><input type="number" name="parts[${partIndex}][discount]" value="0" min="0" max="100" onchange="validateDiscount(this)"></td>
            <td><input type="number" name="parts[${partIndex}][discount_amount]" readonly></td> 
            <td><input type="number" name="parts[${partIndex}][total]" step="0.01" readonly></td>
            <td><button type="button" onclick="this.closest('tr').remove(); calculateGrandTotal();">Remove</button></td>
        `;

        table.appendChild(row);
        partIndex++;
    }

    function syncFromPartNumber(input) {
        const row = input.closest('tr');
        const partNumber = input.value.trim();
        const part = vehicleParts[partNumber];
        if (part) {
            row.querySelector('.part-name-input').value = part.name;
            row.querySelector('input[name*="[unit_price]"]').value = part.price;
            calculateTotal(row.querySelector('input[name*="[quantity]"]'));
        }
    }

    function syncFromPartName(input) {
        const row = input.closest('tr');
        const partName = input.value.trim();
        const match = Object.entries(vehicleParts).find(([num, data]) => data.name === partName);
        if (match) {
            const [partNumber, data] = match;
            row.querySelector('.part-number-input').value = partNumber;
            row.querySelector('input[name*="[unit_price]"]').value = data.price;
            calculateTotal(row.querySelector('input[name*="[quantity]"]'));
        }
    }

    function validateDiscount(input) {
    let val = parseFloat(input.value || 0);
    if (val > 100 || val < 0) {
        alert("Discount should be between 0 and 100%");
        input.value = 0;
        input.style.border = '2px solid red';
        setTimeout(() => input.style.border = '', 1500);
    }
    calculateTotal(input);
}


function calculateTotal(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value || 0);
    const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value || 0);
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value || 0);

    if (discount > 100) {
        alert("Discount cannot exceed 100%");
        row.querySelector('input[name*="[discount]"]').value = 0;
        return;
    }

    const subtotal = qty * price;
    const discountAmount = (subtotal * discount) / 100;
    const total = subtotal - discountAmount;

    row.querySelector('input[name*="[discount_amount]"]').value = discountAmount.toFixed(2);
    row.querySelector('input[name*="[total]"]').value = total.toFixed(2);

    calculateGrandTotal();
}


    

    function addCostRow() {
        const table = document.querySelector('#costs-table tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="other_costs[${costIndex}][description]" required></td>
            <td><input type="number" name="other_costs[${costIndex}][price]" step="0.01" min="0" value="0" onchange="calculateGrandTotal()" required></td>
            <td><button type="button" onclick="this.closest('tr').remove(); calculateGrandTotal();">Remove</button></td>
        `;
        table.appendChild(row);
        costIndex++;
    }

    function calculateGrandTotal() {
        let partTotal = 0;
        document.querySelectorAll('input[name*="[total]"]').forEach(input => {
            partTotal += parseFloat(input.value || 0);
        });

        let costTotal = 0;
        document.querySelectorAll('input[name*="[price]"]').forEach(input => {
            costTotal += parseFloat(input.value || 0);
        });

        const grandTotal = partTotal + costTotal;
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }

    function showConfirmation() {
        calculateGrandTotal();
        const content = document.getElementById('confirmation-content');
        const customer_name = document.getElementById('customer_name').value;
        const date = document.getElementById('date').value;
        const grand_total = parseFloat(document.getElementById('grand_total').value || 0);

        let html = `<p><strong>Invoice No:</strong> ${generatedInvoiceNo}</p>`;
        html += `<p><strong>Customer Name:</strong> ${customer_name}</p>`;
        html += `<p><strong>Date:</strong> ${date}</p>`;
        html += `<h4>Sold Parts:</h4><ul>`;
        document.querySelectorAll('#parts-table tbody tr').forEach(row => {
            const pn = row.querySelector('.part-number-input')?.value || '';
            const name = row.querySelector('.part-name-input')?.value || '';
            const qty = row.querySelector('input[name*="[quantity]"]')?.value || '';
            const discount = row.querySelector('input[name*="[discount]"]')?.value || '0';
            const total = row.querySelector('input[name*="[total]"]')?.value || '';
            html += `<li>${pn} - ${name} | Qty: ${qty} | Discount: ${discount}% | Total: ${total}</li>`;
        });
        html += `</ul><h4>Other Costs:</h4><ul>`;
        document.querySelectorAll('#costs-table tbody tr').forEach(row => {
            const desc = row.querySelector('input[name*="[description]"]')?.value || '';
            const price = row.querySelector('input[name*="[price]"]')?.value || '';
            html += `<li>${desc}: ${price}</li>`;
        });
        html += `</ul><p><strong>Grand Total:</strong> ${grand_total.toFixed(2)}</p>`;

        content.innerHTML = html;
        document.getElementById('confirmation-popup').style.display = 'block';
    }

    function hidePopup() {
        document.getElementById('confirmation-popup').style.display = 'none';
    }

    function downloadConfirmation() {
        const content = document.getElementById('confirmation-content').innerHTML;
        const html = `<html><head><title>Invoice Preview</title></head><body>${content}</body></html>`;
        const blob = new Blob([html], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Invoice_Preview.html';
        a.click();
        URL.revokeObjectURL(url);
    }

    window.onload = () => {
        addPartRow();
        addCostRow();
    };
</script>

<!-- Autocomplete lists -->
<datalist id="partNumbers">
    @foreach($parts as $part)
        <option value="{{ $part->part_number }}">
    @endforeach
</datalist>

<datalist id="partNames">
    @foreach($parts as $part)
        <option value="{{ $part->part_name }}">
    @endforeach
</datalist>

@endsection
