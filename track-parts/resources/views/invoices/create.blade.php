@extends('layouts.app')

@if(Auth::guard('dealer')->check())
    <p>Logged in as: {{ Auth::guard('dealer')->user()->name }} (ID: {{ Auth::guard('dealer')->user()->id }})</p>
@endif


@if(session('error'))
    <div style="background-color: #ffcccc; color: #a94442; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
         {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">
        {{ session('success') }}
    </div>
@endif

@section('content')
<h2>Create Invoice</h2>

<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    <input type="hidden" name="invoice_no" id="invoice_no_hidden">



<input type="text" name="contact_number" id="contact_number" placeholder="Contact Number" maxlength="10"
       pattern="\d{10}" title="Enter exactly 10 digits"
       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required autocomplete="off">

    <div id="contactSuggestions" style="border: 1px solid #ccc; display: none;"></div>

    <input type="text" name="customer_name" id="customer_name"
       placeholder="Customer Name" required
       autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">

<input type="text" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number" required autocomplete="off">
<div id="vehicleSuggestions" style="border: 1px solid #ccc; display: none;"></div>



    <input type="date" name="date" id="date" required>

    <h4>Sold Parts</h4>
    <table id="parts-table">
        <thead>
            <tr>
                <th>Part Number</th>
                <th>Part Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount (%)</th>
                <th>Discount Amount</th> <!-- NEW -->
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
    <div id="otherCostSuggestions" style="position:absolute; background:white; border:1px solid #ccc; display:none; z-index:999;"></div>


    <br><br>
    <label>Grand Total:</label>
    <input type="number" step="0.01" name="grand_total" id="grand_total" readonly>

    <br><br>
    <button type="button" onclick="showConfirmation()">Submit Invoice</button>
</form>

<!-- Confirmation Popup -->
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

    let partIndex = 0;
    let costIndex = 0;
    const generatedInvoiceNo = 'INV' + Math.floor(100000 + Math.random() * 900000);
    document.getElementById('invoice_no_hidden').value = generatedInvoiceNo;

    function addPartRow() {
        const table = document.querySelector('#parts-table tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><input type="text" class="part-number-input" name="parts[${partIndex}][part_number]" placeholder="Part Number" autocomplete="off" required></td>
    <td><input type="text" class="part-name-input" name="parts[${partIndex}][part_name]" placeholder="Part Name" autocomplete="off" required></td>
            <td><input type="number" name="parts[${partIndex}][quantity]" min="1" value="1" onchange="calculateTotal(this)" required></td>
            <td><input type="number" name="parts[${partIndex}][unit_price]" step="0.01" readonly></td>
            <td><input type="number" name="parts[${partIndex}][discount]" value="0" min="0" max="100" onchange="validateDiscount(this)"></td>
            <td><input type="number" name="parts[${partIndex}][discount_amount]" readonly></td> 
            <td><input type="number" name="parts[${partIndex}][total]" step="0.01" readonly></td>
            <td><button type="button" onclick="this.closest('tr').remove(); calculateGrandTotal();">Remove</button></td>
        `;

        table.appendChild(row);
        partIndex++;
    }

    document.addEventListener("input", function (e) {
    if (e.target.classList.contains('part-number-input') || e.target.classList.contains('part-name-input')) {
        const input = e.target;
        const query = input.value;
        const row = input.closest('tr');

        // remove any existing suggestion box
        document.querySelectorAll('.suggestion-box').forEach(box => box.remove());

        if (query.length >= 2) {
            fetch(`/dealer/parts/search?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const suggestionBox = document.createElement('div');
                        suggestionBox.className = 'suggestion-box';
                        suggestionBox.style.border = '1px solid #ccc';
                        suggestionBox.style.position = 'absolute';
                        suggestionBox.style.background = '#fff';
                        suggestionBox.style.zIndex = 1000;
                        suggestionBox.style.maxHeight = '150px';
                        suggestionBox.style.overflowY = 'auto';

                        const rect = input.getBoundingClientRect();
                        suggestionBox.style.left = rect.left + window.scrollX + 'px';
                        suggestionBox.style.top = rect.bottom + window.scrollY + 'px';
                        suggestionBox.style.width = rect.width + 'px';

                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.textContent = `${item.part_number} - ${item.part_name}`;
                            div.style.padding = '5px';
                            div.style.cursor = 'pointer';
                            div.onclick = () => {
                                row.querySelector('.part-number-input').value = item.part_number;
                                row.querySelector('.part-name-input').value = item.part_name;
                                row.querySelector('input[name*="[unit_price]"]').value = item.price;
                                calculateTotal(row.querySelector('input[name*="[quantity]"]'));
                                suggestionBox.remove();
                            };
                            suggestionBox.appendChild(div);
                        });

                        document.body.appendChild(suggestionBox);

                        document.addEventListener('click', function hideBox(event) {
                            if (!suggestionBox.contains(event.target) && event.target !== input) {
                                suggestionBox.remove();
                                document.removeEventListener('click', hideBox);
                            }
                        });
                    }
                });
        }
    }
});


    

    function validateDiscount(input) {
    let val = parseFloat(input.value || 0);
    if (val > 100 || val < 0) {
        alert("Discount should be between 0 and 100%");
        input.value = 0;
        input.style.border = '2px solid red';
        setTimeout(() => input.style.border = '', 1500);
    }
    calculateTotal(input);
}


function calculateTotal(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value || 0);
    const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value || 0);
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value || 0);

    if (discount > 100) {
        alert("Discount cannot exceed 100%");
        row.querySelector('input[name*="[discount]"]').value = 0;
        return;
    }

    const subtotal = qty * price;
    const discountAmount = (subtotal * discount) / 100;
    const total = subtotal - discountAmount;

    row.querySelector('input[name*="[discount_amount]"]').value = discountAmount.toFixed(2);
    row.querySelector('input[name*="[total]"]').value = total.toFixed(2);

    calculateGrandTotal();
}


    

    function addCostRow() {
        const table = document.querySelector('#costs-table tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="other_costs[${costIndex}][description]" required autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false"></td>
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

        const grandTotal = partTotal + costTotal;
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }

    function showConfirmation() {
    calculateGrandTotal();
    const content = document.getElementById('confirmation-content');

    const customer_name = document.getElementById('customer_name').value.trim();
    const contact_number = document.getElementById('contact_number').value.trim();
    const vehicle_number = document.getElementById('vehicle_number').value.trim();
    const date = document.getElementById('date').value;
    const grand_total = parseFloat(document.getElementById('grand_total').value || 0);

        // Validate required fields
        if (!customer_name || !contact_number || !vehicle_number || !date) {
        alert("Please fill in all required fields:\n- Contact Number\n- Customer Name\n- Vehicle Number\n- Date");
        return;
    }


    let html = `<p><strong>Invoice No:</strong> ${generatedInvoiceNo}</p>`;
    html += `<p><strong>Customer Name:</strong> ${customer_name}</p>`;
    html += `<p><strong>Contact Number:</strong> ${contact_number}</p>`;
    html += `<p><strong>Vehicle Number:</strong> ${vehicle_number}</p>`;
    html += `<p><strong>Date:</strong> ${date}</p>`;

    // Sold Parts Table
    html += `<h4>Sold Parts</h4>`;
    html += `
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Part No</th>
                    <th>Part Name</th>
                    <th>Qty</th>
                    <th>Discount (%)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
    `;
    document.querySelectorAll('#parts-table tbody tr').forEach(row => {
        const pn = row.querySelector('.part-number-input')?.value || '';
        const name = row.querySelector('.part-name-input')?.value || '';
        const qty = row.querySelector('input[name*="[quantity]"]')?.value || '';
        const discount = row.querySelector('input[name*="[discount]"]')?.value || '0';
        const total = row.querySelector('input[name*="[total]"]')?.value || '';
        html += `
            <tr>
                <td>${pn}</td>
                <td>${name}</td>
                <td>${qty}</td>
                <td>${discount}%</td>
                <td>${total}</td>
            </tr>
        `;
    });
    html += `</tbody></table>`;

    // Other Costs Table
    html += `<h4>Other Costs</h4>`;
    html += `
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
    `;
    document.querySelectorAll('#costs-table tbody tr').forEach(row => {
        const desc = row.querySelector('input[name*="[description]"]')?.value || '';
        const price = row.querySelector('input[name*="[price]"]')?.value || '';
        html += `
            <tr>
                <td>${desc}</td>
                <td>${price}</td>
            </tr>
        `;
    });
    html += `</tbody></table>`;

    // Grand Total
    html += `<p><strong>Grand Total:</strong> Rs. ${grand_total.toFixed(2)}</p>`;

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

    window.onload = () => {
        addPartRow();
        addCostRow();
    };





document.addEventListener("DOMContentLoaded", () => {
    const contactInput = document.getElementById('contact_number');
    const suggestionBox = document.getElementById('contactSuggestions');

    contactInput.addEventListener('input', function () {
        const query = this.value;
        if (query.length >= 3) {
            fetch(`/contacts/search?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
                    if (data.length > 0) {
                        suggestionBox.style.display = 'block';
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.textContent = `${item.contact_number} - ${item.customer_name} (${item.vehicle_number || 'No Vehicle'})`;

                            div.style.padding = '5px';
                            div.style.cursor = 'pointer';
                            div.onclick = () => {
                                contactInput.value = item.contact_number;
                                document.getElementById('customer_name').value = item.customer_name;
                                document.getElementById('vehicle_number').value = item.vehicle_number || ''; // updated key
                                suggestionBox.innerHTML = '';
                                suggestionBox.style.display = 'none';
                            };
                            suggestionBox.appendChild(div);
                        });
                    } else {
                        suggestionBox.style.display = 'none';
                    }
                });
        } else {
            suggestionBox.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!suggestionBox.contains(e.target) && e.target !== contactInput) {
            suggestionBox.style.display = 'none';
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const vehicleInput = document.getElementById('vehicle_number');
    const vehicleBox = document.getElementById('vehicleSuggestions');

    if (vehicleInput && vehicleBox) {
        vehicleInput.addEventListener('input', function () {
            const query = this.value;
            if (query.length >= 2) {
                fetch(`/vehicles/search?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        vehicleBox.innerHTML = '';
                        if (data.length > 0) {
                            vehicleBox.style.display = 'block';
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.textContent = item.vehicle_number + ' - ' + item.customer_name;
                                div.style.padding = '5px';
                                div.style.cursor = 'pointer';
                                div.onclick = () => {
                                    vehicleInput.value = item.vehicle_number;
                                    document.getElementById('contact_number').value = item.contact_number;
                                    document.getElementById('customer_name').value = item.customer_name;
                                    vehicleBox.innerHTML = '';
                                    vehicleBox.style.display = 'none';
                                };
                                vehicleBox.appendChild(div);
                            });
                        } else {
                            vehicleBox.style.display = 'none';
                        }
                    });
            } else {
                vehicleBox.style.display = 'none';
            }
        });

        // Hide suggestion box when clicking outside
        document.addEventListener('click', function (e) {
            if (!vehicleBox.contains(e.target) && e.target !== vehicleInput) {
                vehicleBox.style.display = 'none';
            }
        });
    }
});



document.addEventListener('input', function(e) {
    if (e.target.name.includes("other_costs") && e.target.name.includes("[description]")) {
        const input = e.target;
        const query = input.value;

        if (query.length >= 2) {
            fetch(`/other-costs/suggestions?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    const suggestionBox = document.getElementById('otherCostSuggestions');
                    suggestionBox.innerHTML = '';
                    if (data.length > 0) {
                        const rect = input.getBoundingClientRect();
                        suggestionBox.style.left = rect.left + 'px';
                        suggestionBox.style.top = rect.bottom + window.scrollY + 'px';
                        suggestionBox.style.width = input.offsetWidth + 'px';
                        suggestionBox.style.display = 'block';

                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.textContent = `${item.description} - Rs. ${item.price}`;
                            div.style.padding = '5px';
                            div.style.cursor = 'pointer';

                            div.onclick = () => {
                                input.value = item.description;
                                const row = input.closest('tr');
                                row.querySelector('input[name*="[price]"]').value = item.price;
                                suggestionBox.style.display = 'none';
                            };

                            suggestionBox.appendChild(div);
                        });
                    } else {
                        suggestionBox.style.display = 'none';
                    }
                });
        } else {
            document.getElementById('otherCostSuggestions').style.display = 'none';
        }
    }
});

// Hide suggestion box on click outside
document.addEventListener('click', function(e) {
    const box = document.getElementById('otherCostSuggestions');
    if (!box.contains(e.target)) {
        box.style.display = 'none';
    }
});

//capitalize the first letter
document.getElementById('customer_name').addEventListener('input', function () {
    let val = this.value.toLowerCase(); // Start with all lowercase
    this.value = val.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
});




</script>
@endsection

