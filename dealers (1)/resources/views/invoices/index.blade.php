<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice History</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background-color: #888;
    }
    h2 {
        color: white;
        text-align: center;
        margin-bottom: 20px;
    }
    .filter-form {
        display: flex;
        justify-content: right;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .filter-form input[type="text"],
    .filter-form input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
    }
    .filter-form button {
        padding: 8px 12px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 4px;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    background-color: #000;
    color: rgb(255, 255, 255);
    border: 1px solid #555;
}
    table th {
    background-color: #000;
    color: red; /* Header text color */
    padding: 10px;
    border-right: 1px solid #444;
    border-bottom: 2px solid rgb(255, 255, 255); /* Underline for the header row */
}

table td {
    color: white; /* Data text color */
    padding: 10px;
    border-right: 1px solid #444;
    border-bottom: 1px solid #555;
}


table th:last-child,
table td:last-child {
    border-right: none;
}


/* Header underline */
table thead tr {
    border-bottom: 2px solid red; /* strong underline for header row */
}

table tbody tr:not(:last-child) {
    border-bottom: 1px solid #555; /* row separation for data rows */
}



    #invoiceSuggestions {
        position: absolute;
        background: #222;
        color: white;
        border: 1px solid #444;
        max-height: 150px;
        overflow-y: auto;
        z-index: 10;
    }

    #invoiceSuggestions div:hover {
        background-color: #555;
        cursor: pointer;
    }

    .icon-btn {
        color: cyan;
        font-size: 18px;
        text-decoration: none;
    }
    @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
    }

    .action-btn {
    padding: 5px 12px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.cancel-btn {
    background-color: #dc3545;
    color: #fff;
}

.cancel-btn:hover {
    background-color: #c82333;
}

.view-btn {

    color: #00ffff;
}

.view-btn:hover {

    color: #00e6e6;
}

.download-btn {
   
    color: #fff;
}

.download-btn:hover {
    
      color: #00e6e6;
    

}
.cancel-label {
    color:rgb(255, 255, 255);
    font-size: 14px;
    background-color: #444;
    padding: 5px 10px;
    border-radius: 5px;
}


    
</style>
</head>
<body>
<div class="position-relative">
    <div class="d-flex align-items-center justify-content-center p-3 mb-2 bg-black position-relative">
    <!-- Back Button aligned vertically center -->
     <a href="{{ url('/dealer/dashboard') }}" class="btn btn-outline-light position-absolute start-0 ms-3">
        &#8249;
    </a>

    <!-- Logo beside the button (optional) -->
    <img src="{{ asset('images/logo.png') }}" class="position-absolute start-0 ms-5" style="height: 50px; left: 50px;"/>

    <!-- Centered Title -->
    <h1 class="text-center text-white w-100">Invoice History</h1>
</div>

  </div>

@if(session('success'))
    <div style="
        background: linear-gradient(90deg,rgb(64, 178, 91) 0%,rgb(137, 205, 152) 100%);
        color: #fff;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 16px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: fadeIn 0.5s ease-in-out;">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="white">
            <path d="M12 0C5.371 0 0 5.373 0 12s5.371 12 12 12 12-5.373 12-12S18.629 0 12 0zm-1.2 18l-5.3-5.3L7.5 11l3.3 3.3L16.5 8.6 18 10l-7.2 8z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('invoices.index') }}" class="filter-form" autocomplete="off">
    <div style="position: relative;">
    <input type="text" name="invoice_no" id="invoiceSearch" placeholder="Invoice No" value="{{ request('invoice_no') }}">
    <div id="invoiceSuggestions" style="display: none;"></div>
</div>


     <div style="position: relative;">
        <input type="text" name="vehicle_number" id="vehicleSearch" placeholder="Vehicle No" value="{{ request('vehicle_number') }}">
        <div id="vehicleSuggestions" style="display: none;"></div>
    </div>

    <input type="date" name="date" value="{{ request('date') }}">

    <button type="submit">Search</button>
    <a href="{{ route('invoices.index') }}"><button type="button" style="background: #555;">Reset</button></a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice No</th>
            <th>Customer </th>
            <th>Vehicle No</th>
            <th>Make</th>
            <th>Model</th>
            <th>ODO</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
            <th style="text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->customer_name }}</td>
                <td>{{ $invoice->vehicle_number }}</td>
                <td>{{ $invoice->make }}</td>
                <td>{{ $invoice->vehicle_model }}</td>
                <td>{{ $invoice->odo_value }} KM</td>
                <td>{{ $invoice->date }}</td>
                <td>{{ $invoice->status }}</td>
                <td>Rs.{{ number_format($invoice->grand_total, 2) }}</td>
                
<td style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">

    @if($invoice->status !== 'Cancelled')
        <form action="{{ route('invoices.cancel', $invoice->invoice_no) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this invoice?');" style="margin: 0;">
            @csrf
            @method('PUT')
            <button type="submit" class="action-btn cancel-btn">Cancel</button>
        </form>
    @else
         <span class="text-muted cancel-label">Cancelled</span>
    @endif

    <a href="#" class="action-btn view-btn" onclick="showInvoice('{{ $invoice->invoice_no }}')">👁️ View</a>
    <a href="{{ route('invoices.download', $invoice->invoice_no) }}" class="action-btn download-btn">⬇️ Download</a>

</td>


            </tr>
        @empty
            <tr><td colspan="9">No invoices found.</td></tr>
        @endforelse
    </tbody>
</table>

<!-- Modal -->
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
    let suggestionBox = document.getElementById('invoiceSuggestions');

    if (query.length < 1) {
        suggestionBox.style.display = 'none';
        return;
    }

    fetch(`/autocomplete-invoices?term=${query}`)
        .then(res => res.json())
        .then(data => {
            suggestionBox.innerHTML = '';
            if (data.length) {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.textContent = item;
                    div.style.padding = '5px';
                    div.onclick = () => {
                        document.getElementById('invoiceSearch').value = item;
                        suggestionBox.style.display = 'none';
                    };
                    suggestionBox.appendChild(div);
                });
                suggestionBox.style.display = 'block';
            } else {
                suggestionBox.style.display = 'none';
            }
        });
});
setupAutocomplete('invoiceSearch', 'invoiceSuggestions', '/autocomplete-invoices');

setupAutocomplete('vehicleSearch', 'vehicleSuggestions', '/autocomplete-vehicles');
</script>
</body>
</html>
