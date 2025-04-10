@extends('layouts.app')

@section('content')
<h2>Invoices List</h2>


<form method="GET" action="{{ route('invoices.index') }}" style="margin-bottom: 20px;">

<input type="text" name="invoice_no" id="invoiceSearch" placeholder="Search Invoice No" autocomplete="off">
<div id="invoiceSuggestions" style="position: absolute; background: white; border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none;"></div>


    <input type="date" name="date" value="{{ request('date') }}">
    <button type="submit">Search</button>
    <a href="{{ route('invoices.index') }}"><button type="button">Reset</button></a>
</form>


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


document.getElementById('invoiceSearch').addEventListener('keyup', function () {
    let query = this.value;

    if (query.length < 1) {
        document.getElementById('invoiceSuggestions').style.display = 'none';
        return;
    }

    fetch(`/autocomplete-invoices?term=${query}`)
        .then(res => res.json())
        .then(data => {
            let suggestionsBox = document.getElementById('invoiceSuggestions');
            suggestionsBox.innerHTML = '';

            if (data.length) {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.textContent = item;
                    div.style.padding = '5px';
                    div.style.cursor = 'pointer';
                    div.addEventListener('click', function () {
                        document.getElementById('invoiceSearch').value = item;
                        suggestionsBox.style.display = 'none';
                    });
                    suggestionsBox.appendChild(div);
                });
                suggestionsBox.style.display = 'block';
            } else {
                suggestionsBox.style.display = 'none';
            }
        });
});



</script>


@endsection
