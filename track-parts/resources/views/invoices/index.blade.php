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
                    <a href="{{ route('invoices.show', $invoice->invoice_no) }}">👁️ View</a>
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
@endsection
