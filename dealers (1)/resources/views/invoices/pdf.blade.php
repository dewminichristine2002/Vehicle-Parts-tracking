<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Nunito Sans', sans-serif;
            padding: 0;
            margin: 0;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            padding-top: 20px;
        }
        .header {
            position: relative;
            height: 110px;
            width: 100%;
            background: none;
            overflow: hidden;
            margin-bottom: 30px;
        }
         .header-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background-color: #000;
    clip-path: polygon(0 0, 100% 0, 90% 100%, 0% 100%);
}

.header-bg-red {
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background-color: #ED1D26;
    clip-path: polygon(10% 0, 100% 0, 100% 100%, 0% 100%);

        }
        .logo-container {
            position: absolute;
            left: 0;
            top: 20px;
            margin-left: 3rem;
            height: 50px;
            display: flex;
            align-items: center;
        }
        .logo-placeholder {
            font-size: 24px;
            font-weight: bold;
            color: white;
            padding: 5px;
        }
        .invoice-title {
            position: absolute;
            right: 0;
            top: 20px;
            margin-right: 3rem;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }
        .customer-details {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .customer-details-row {
            display: table-row;
        }
        .customer-details-left, .customer-details-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .detail-item {
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: baseline; /* This ensures perfect vertical alignment */
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0; /* Prevents the label from shrinking */
        }
        .detail-value {
            flex-grow: 1;
        }
        /* Rest of your CSS remains unchanged */
        .parts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .parts-table th {
            background-color: #ED1D26;
            color: white;
            padding: 8px;
            text-align: center;
        }
        .parts-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .parts-table tr:nth-child(even) {
            background-color: #f8d7da;
        }
        .parts-table tr:nth-child(odd) {
            background-color: white;
        }
        .other-costs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .other-costs-table th {
            background-color: #231F20;
            color: white;
            padding: 8px;
            text-align: center;
        }
        .other-costs-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .other-costs-table tr:nth-child(even) {
            background-color: #b1a0a0;
        }
        .other-costs-table tr:nth-child(odd) {
            background-color: #928181;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            width: 200px;
            font-weight: bold;
        }
        .logo-container img {
    max-height: 100%;
    width: auto;
    object-fit: contain;
}
    </style>
</head>
<body>
    <div class="header">
        <div class="header-bg"></div>
        <div class="header-bg-red"></div>
        <div class="logo-container">
            <img src="{{ public_path('../images/logo.png') }}" alt="Motors Logo" style="height: 50px;">
        </div>
        <h1 class="invoice-title">INVOICE</h1>
    </div>
    
    <div class="container">
        <div class="customer-details">
            <div class="customer-details-row">
                <div class="customer-details-left">
                    <div class="detail-item">
                        <span class="detail-label">Customer Name:</span>
                        <span class="detail-value">{{ $invoice->customer_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Contact Number:</span>
                        <span class="detail-value">{{ $invoice->contact_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Vehicle Number:</span>
                        <span class="detail-value">{{ $invoice->vehicle_number }}</span>
                    </div>
                    @if($invoice->odo_value)
                    <div class="detail-item">
                        <span class="detail-label">ODO:</span>
                        <span class="detail-value">{{ $invoice->odo_value }} @if($invoice->odo_type)({{ $invoice->odo_type }})@endif</span>
                    </div>
                    @endif
                </div>
                <div class="customer-details-right">
                    <div class="detail-item">
                        <span class="detail-label">Invoice Number:</span>
                        <span class="detail-value">{{ $invoice->invoice_no }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $invoice->date }}</span>
                    </div>
                    @if($invoice->make)
                    <div class="detail-item">
                        <span class="detail-label">Make:</span>
                        <span class="detail-value">{{ $invoice->make }}</span>
                    </div>
                    @endif
                    @if($invoice->vehicle_model)
                    <div class="detail-item">
                        <span class="detail-label">Vehicle Model:</span>
                        <span class="detail-value">{{ $invoice->vehicle_model }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rest of your tables remain unchanged -->
        <table class="parts-table">
            <thead>
                <tr>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Discount</th>
                    <th>Dsc. Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->soldParts as $part)
                    @php
                        $quantity = $part->quantity ?? 0;
                        $unitPrice = $part->unit_price ?? 0;
                        $discount = $part->discount ?? 0;
                        $discountAmount = ($quantity * $unitPrice * $discount) / 100;
                        $total = ($quantity * $unitPrice) - $discountAmount;
                    @endphp
                    <tr>
                        <td>{{ $part->part_number }}</td>
                        <td>{{ $part->part_name }}</td>
                        <td>{{ number_format($unitPrice, 2) }}</td>
                        <td>{{ $quantity }}</td>
                        <td>{{ number_format($discount, 2) }}%</td>
                        <td>{{ number_format($discountAmount, 2) }}</td>
                        <td>{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($invoice->otherCosts->count())
            <table class="other-costs-table">
                <thead>
                    <tr>
                        <th>Others</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Dsc. Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->otherCosts as $cost)
                        @php
                            $price = $cost->price ?? 0;
                            $discount = $cost->discount ?? 0;
                            $discountAmount = ($price * $discount) / 100;
                            $total = $price - $discountAmount;
                        @endphp
                        <tr>
                            <td>{{ $cost->description }}</td>
                            <td>{{ number_format($price, 2) }}</td>
                            <td>{{ number_format($discount, 2) }}%</td>
                            <td>{{ number_format($discountAmount, 2) }}</td>
                            <td>{{ number_format($total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="total-section">
            <input type="text" class="total-input" readonly value="Grand Total: Rs. {{ number_format($invoice->grand_total, 2) }}">
        </div>
    </div>
</body>
</html>