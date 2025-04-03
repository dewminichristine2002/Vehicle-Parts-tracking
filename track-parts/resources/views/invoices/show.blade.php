@extends('layouts.app')

@section('content')
    <h2>Invoice Details - {{ $invoice->invoice_no }}</h2>

    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Contact Number:</strong> {{ $invoice->contact_number }}</p>
    <p><strong>Vehicle Number:</strong> {{ $invoice->vehicle_number }}</p>
    <p><strong>Date:</strong> {{ $invoice->date }}</p>
    <p><strong>Grand Total:</strong> Rs. {{ number_format($invoice->grand_total, 2) }}</p>

    <h4>Sold Parts</h4>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Part No</th>
                <th>Part Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount (%)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->soldParts as $part)
                <tr>
                    <td>{{ $part->part_number }}</td>
                    <td>{{ $part->part_name }}</td>
                    <td>{{ $part->quantity }}</td>
                    <td>{{ number_format($part->unit_price, 2) }}</td>
                    <td>{{ number_format($part->discount ?? 0, 2) }}</td>
                    <td>{{ number_format($part->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($invoice->otherCosts->count())
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
                        <td>{{ number_format($cost->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <br>
    <a href="{{ route('invoices.download', $invoice->invoice_no) }}">📥 Download PDF</a>
    <br><br>
    <a href="{{ route('invoices.index') }}">← Back to List</a>
@endsection
