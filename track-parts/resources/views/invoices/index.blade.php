@extends('layouts.app')

@section('content')
<h2>Invoices List</h2>

@if (session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Grand Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->customer_name }}</td>
                <td>{{ $invoice->date }}</td>
                <td>{{ number_format($invoice->grand_total, 2) }}</td>
                <td>
                    <a href="#" onclick="showInvoice('{{ $invoice->invoice_no }}')">👁️ View</a>

                    |
                    <a href="{{ route('invoices.download', $invoice->invoice_no) }}">⬇️ Download</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No invoices found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<!-- Modal for invoice preview -->
<div id="invoiceModal" style="display:none; position:fixed; top:10%; left:10%; width:80%; height:80%; background:#fff; border:2px solid #000; padding:20px; overflow:auto; z-index:9999;">
<div id="invoiceModalContent">Loading...</div>

<br>
<div style="margin-top: 10px;">
    <button onclick="document.getElementById('invoiceModal').style.display='none'">Close</button>
    <a id="downloadLink" href="#" target="_blank" style="margin-left: 10px;">
        <button>Download PDF</button>
    </a>
</div>

</div>

<script>
function showInvoice(invoiceNo) {
    fetch(`/invoices/${invoiceNo}/preview`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('invoiceModalContent').innerHTML = html;
            document.getElementById('downloadLink').href = `/invoices/${invoiceNo}/download`;
            document.getElementById('invoiceModal').style.display = 'block';
        })
        .catch(error => {
            console.error("Error loading invoice:", error);
            alert("Failed to load invoice details.");
        });
}

</script>

@endsection
