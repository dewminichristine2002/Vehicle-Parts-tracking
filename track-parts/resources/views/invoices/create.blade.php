@extends('layouts.app')

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
    <label>Discount (%):</label>
    <input type="number" step="0.01" min="0" max="100" name="discount" id="discount" value="0" required>

    <label>Grand Total:</label>
    <input type="number" step="0.01" name="grand_total" id="grand_total" readonly>

    <br><br>
    <button type="button" onclick="showConfirmation()">Submit Invoice</button>
</form>

<!-- ✅ Confirmation Popup -->
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
        vehicleParts["{{ $vp->part_number }}"] = {{ $vp->unit_price }};
    @endforeach

    let partIndex = 0;
    let costIndex = 0;
    const generatedInvoiceNo = 'INV' + Math.floor(100000 + Math.random() * 900000);
    document.getElementById('invoice_no_hidden').value = generatedInvoiceNo;

    function addPartRow() {
        const table = document.querySelector('#parts-table tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <select name="parts[${partIndex}][part_number]" onchange="updatePartRow(this)">
                    <option value="">Select</option>
                    ${partsData.map(part => `<option value="${part.part_number}">${part.part_number}</option>`).join('')}
                </select>
            </td>
            <td><input type="text" name="parts[${partIndex}][part_name]" readonly></td>
            <td><input type="number" name="parts[${partIndex}][quantity]" min="1" value="1" onchange="calculateTotal(this)" required></td>
            <td><input type="number" name="parts[${partIndex}][unit_price]" step="0.01" readonly></td>
            <td><input type="number" name="parts[${partIndex}][total]" step="0.01" readonly></td>
            <td><button type="button" onclick="this.closest('tr').remove(); calculateGrandTotal();">Remove</button></td>
        `;

        table.appendChild(row);
        partIndex++;
    }

    function updatePartRow(select) {
        const row = select.closest('tr');
        const partNumber = select.value;
        const part = partsData.find(p => p.part_number === partNumber);

        if (part) {
            row.querySelector('input[name*="[part_name]"]').value = part.part_name;
            row.querySelector('input[name*="[unit_price]"]').value = vehicleParts[partNumber] || 0;
            calculateTotal(row.querySelector('input[name*="[quantity]"]'));
        }
    }

    function calculateTotal(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value || 0);
        const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value || 0);
        const total = qty * price;
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

        let discountPercent = parseFloat(document.getElementById('discount').value || 0);
        if (discountPercent > 100) discountPercent = 100;

        const discountAmount = ((partTotal + costTotal) * discountPercent) / 100;
        const grandTotal = partTotal + costTotal - discountAmount;

        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }

    document.getElementById('discount').addEventListener('input', calculateGrandTotal);

    function showConfirmation() {
        calculateGrandTotal();

        const content = document.getElementById('confirmation-content');
        const customer_name = document.getElementById('customer_name').value;
        const date = document.getElementById('date').value;
        const discount = parseFloat(document.getElementById('discount').value || 0);
        const grand_total = parseFloat(document.getElementById('grand_total').value || 0);

        let partTotal = 0;
        let costTotal = 0;
        document.querySelectorAll('input[name*="[total]"]').forEach(input => {
            partTotal += parseFloat(input.value || 0);
        });
        document.querySelectorAll('input[name*="[price]"]').forEach(input => {
            costTotal += parseFloat(input.value || 0);
        });
        const discountAmount = ((partTotal + costTotal) * discount) / 100;

        let html = `<p><strong>Invoice No (Auto-generated):</strong> ${generatedInvoiceNo}</p>`;
        html += `<p><strong>Customer Name:</strong> ${customer_name}</p>`;
        html += `<p><strong>Date:</strong> ${date}</p>`;
        html += `<h4>Sold Parts:</h4><ul>`;
        document.querySelectorAll('#parts-table tbody tr').forEach(row => {
            const pn = row.querySelector('select[name*="[part_number]"]')?.value || '';
            const name = row.querySelector('input[name*="[part_name]"]')?.value || '';
            const qty = row.querySelector('input[name*="[quantity]"]')?.value || '';
            const total = row.querySelector('input[name*="[total]"]')?.value || '';
            html += `<li>${pn} - ${name} | Qty: ${qty} | Total: ${total}</li>`;
        });
        html += `</ul><h4>Other Costs:</h4><ul>`;
        document.querySelectorAll('#costs-table tbody tr').forEach(row => {
            const desc = row.querySelector('input[name*="[description]"]')?.value || '';
            const price = row.querySelector('input[name*="[price]"]')?.value || '';
            html += `<li>${desc}: ${price}</li>`;
        });
        html += `</ul><p><strong>Discount (%):</strong> ${discount}%</p>`;
        html += `<p><strong>Discount Amount:</strong> ${discountAmount.toFixed(2)}</p>`;
        html += `<p><strong>Grand Total:</strong> ${grand_total.toFixed(2)}</p>`;

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
</script>
@endsection
