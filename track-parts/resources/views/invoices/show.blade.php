@extends('layouts.app')

@section('content')
    <h2>Invoice Details - {{ $invoice->invoice_no }}</h2>

    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Date:</strong> {{ $invoice->date }}</p>
    <p><strong>Discount:</strong> {{ $invoice->discount }}%</p>
    <p><strong>Grand Total:</strong> Rs. {{ number_format($invoice->grand_total, 2) }}</p>

    <h4>Sold Parts</h4>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Part No</th>
                <th>Part Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->soldParts as $part)
                <tr>
                    <td>{{ $part->part_number }}</td>
                    <td>{{ $part->part_name }}</td>
                    <td>{{ $part->quantity }}</td>
                    <td>{{ $part->unit_price }}</td>
                    <td>{{ $part->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Other Costs</h4>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Description</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->otherCosts as $cost)
                <tr>
                    <td>{{ $cost->description }}</td>
                    <td>{{ $cost->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('invoices.download', $invoice->invoice_no) }}">📥 Download PDF</a>
    <br><br>
    <a href="{{ route('invoices.index') }}">← Back to List</a>
@endsection
