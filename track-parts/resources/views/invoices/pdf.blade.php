<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Invoice #{{ $invoice->invoice_no }}</h2>
    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Contact Number:</strong> {{ $invoice->contact_number }}</p>
    <p><strong>Vehicle Number:</strong> {{ $invoice->vehicle_number }}</p>
    <p><strong>Date:</strong> {{ $invoice->date }}</p>

    <h4>Sold Parts</h4>
    <table>
        <thead>
            <tr>
                <th>Part No</th>
                <th>Name</th>
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

    @if ($invoice->otherCosts->count())
        <h4>Other Costs</h4>
        <table>
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
    <p><strong>Grand Total:</strong> {{ number_format($invoice->grand_total, 2) }}</p>
</body>
</html>
