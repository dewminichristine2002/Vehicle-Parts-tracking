<!DOCTYPE html>
<html>
<head>
    <title>GRN {{ $grn->grn_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Goods Received Note</h1>
        <h2>GRN: {{ $grn->grn_number }}</h2>
    </div>

    <div class="details">
        <p><strong>Dealer:</strong> {{ $grn->dealer->company_name }}</p>
        <p><strong>Date:</strong> {{ $grn->grn_date->format('d/m/Y') }}</p>
        <p><strong>Invoice Number:</strong> {{ $grn->invoice_number }}</p>
    </div>

    <table>
    <thead>
        <tr>
            <th>Part Number</th>
            <th>Part Name</th>
            <th>Quantity</th>
            <th>Unit Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grn->items as $item)
        <tr>
            <td>{{ $item->globalPart->part_number }}</td>
            <td>{{ $item->globalPart->part_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->grn_unit_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total" style="margin-top: 20px; text-align: right;">
    <p>Total Items: {{ $grn->items->count() }}</p>
    <p>Total Quantity: {{ $grn->items->sum('quantity') }}</p>
    <p>Total Value: {{ number_format($grn->items->sum(function($item) { return $item->quantity * $item->grn_unit_price; }), 2) }}</p>
</div>
</body>
</html>