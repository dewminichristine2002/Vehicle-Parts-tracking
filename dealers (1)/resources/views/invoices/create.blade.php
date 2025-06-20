@extends('layouts.app')



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
<!-- Add this before your closing </body> tag -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


<link href="{{ asset('css/invoice.css') }}" rel="stylesheet">

<div class="container-fluid p-0">
    <div class="d-flex header ">
        <img src="../images/black.png" class="position-absolute img-fluid w-50" /> <img src="../images/red.png" class="position-absolute img-fluid w-50 end-0" />
        <button onclick="history.back()" class="btn btn-outline-light position-absolute top-0 start-0 m-3"> &#8249;</button>
        <img src="../images/logo.webp" class="position-absolute start-0 mt-3 ms-5" style="height: 50px;" />
        <h1 class="position-absolute end-0 mt-3 me-5 text-light fw-bold"> INVOICE</h1>
    </div>
  </div>

<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    <input type="hidden" name="invoice_no" id="invoice_no_hidden">


<div class="form-grid">
  <input type="text" name="customer_name" id="customer_name"
         placeholder="Customer Name" required autocomplete="off" autocapitalize="off"
         autocorrect="off" spellcheck="false">

         <div style="position: relative;">
  <input type="text" name="contact_number" id="contact_number" placeholder="Contact Number" maxlength="10"
         pattern="\d{10}" title="Enter exactly 10 digits"
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required autocomplete="off">
         <div id="contactSuggestions"></div>
         </div>

         <div style="position: relative;">
  <input type="text" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number" required autocomplete="off">
  <div id="vehicleSuggestions"></div>
  </div>

  <input type="text" id="make" name="make" placeholder="Make" readonly>

  
<div style="position: relative;">
  <input type="text" id="vehicle_model" name="vehicle_model" placeholder="Vehicle Model" autocomplete="off">
  <div id="vehicle-suggestions"></div>
</div>
<input type="number" name="odo_value" step="0.01" min="0" placeholder="ODO Value">


  <select name="odo_type" required>
      <option value="">Select ODO Type</option>
      <option value="per_km">Per Kilometer</option>
      <option value="per_mile">Per Mile</option>
  </select>

  <input type="date" name="date" id="date" required>
</div>


<div id="vehicleSuggestions" class="suggest-box"></div>

<br>

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
    <button type="button" class="add-btn" onclick="addPartRow()">
  <i class="bi bi-plus-lg"></i> Add
</button>

<br>
    
<table id="costs-table">
    <thead>
        <tr>
            <th>Other Costs</th>
            <th>Price</th>
            <th>Discount (%)</th>
            <th>Discount Amount</th>
            <th>Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<button type="button" class="add-btn" onclick="addCostRow()">
  <i class="bi bi-plus-lg"></i> Add
</button>

    <div id="otherCostSuggestions" style="position:absolute; background:white; border:1px solid #ccc; display:none; z-index:999;"></div>


    
    <div id="grand-total-section" class="grand-total-container">
  <label for="grand_total">Grand Total:</label>
  <input type="number" step="0.01" name="grand_total" id="grand_total" readonly>
</div>


    
    <div class="preview-container">
  <button type="button" class="preview-btn" onclick="showConfirmation()">Preview</button>
</div>


</form>

<!-- Confirmation Popup -->
<div id="confirmation-popup" class="modal" tabindex="-1" style="display: none; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content p-4" style="border-radius: 15px; margin: auto; max-width: 90%; max-height: 90vh; overflow-y: auto;">
            <div class="modal-body" id="confirmation-content"></div>
            <div class="text-center mt-4">
                <button onclick="hidePopup()" class="modal-btn" style="background-color: #ED1D26;">Back</button>
                <button onclick="downloadConfirmation()" class="modal-btn" style="background-color: #126F26;">Download</button>
                <button onclick="document.getElementById('invoice-form').submit();" class="modal-btn" style="background-color: #231F20;">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            <td><input type="number" placeholder="Unit Price" name="parts[${partIndex}][unit_price]" step="0.01" readonly></td>
            <td><input type="number"  name="parts[${partIndex}][discount]" value="0" min="0" max="100" onchange="validateDiscount(this)"></td>
            <td><input type="number" name="parts[${partIndex}][discount_amount]" readonly></td> 
            <td><input type="number" name="parts[${partIndex}][total]" step="0.01" readonly></td>
            
    <td>
    <button type="button" class="remove-btn" onclick="this.closest('tr').remove(); calculateGrandTotal();">
        <i class="bi bi-trash-fill"></i>
    </button>
    </td>
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
        <td><input type="text" placeholder="Description" name="other_costs[${costIndex}][description]" required></td>
        <td><input type="number" placeholder="Price" name="other_costs[${costIndex}][price]" step="0.01" min="0" value="0" onchange="calculateCostTotal(this)"></td>
        <td><input type="number" placeholder="Discount(%)" name="other_costs[${costIndex}][discount]" min="0" max="100" value="0" onchange="calculateCostTotal(this)"></td>
        <td><input type="number" name="other_costs[${costIndex}][discount_amount]" readonly></td>
        <td><input type="number" name="other_costs[${costIndex}][total]" readonly></td>
<td>
  <button type="button" class="remove-cost-btn" onclick="this.closest('tr').remove(); calculateGrandTotal();">
    <i class="bi bi-trash-fill"></i>
  </button>
</td>


    `;
    table.appendChild(row);
    costIndex++;
}

function calculateCostTotal(input) {
    const row = input.closest('tr');
    const price = parseFloat(row.querySelector('input[name*="[price]"]').value || 0);
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value || 0);

    const discountAmount = (price * discount) / 100;
    const total = price - discountAmount;

    row.querySelector('input[name*="[discount_amount]"]').value = discountAmount.toFixed(2);
    row.querySelector('input[name*="[total]"]').value = total.toFixed(2);

    calculateGrandTotal();
}


function calculateGrandTotal() {
    let partTotal = 0;
    let costTotal = 0;

    // Sold Parts Total
    document.querySelectorAll('input[name^="parts"][name$="[total]"]').forEach(input => {
        partTotal += parseFloat(input.value || 0);
    });

    // Other Costs Total
    document.querySelectorAll('input[name^="other_costs"][name$="[total]"]').forEach(input => {
        costTotal += parseFloat(input.value || 0);
    });

    const grandTotal = partTotal + costTotal;
    document.getElementById('grand_total').value = grandTotal.toFixed(2);
}




    function showConfirmation() {

        const partRows = document.querySelectorAll('#parts-table tbody tr');
let hasValidPart = false;

for (let row of partRows) {
    const partNumber = row.querySelector('input[name*="[part_number]"]')?.value.trim();
    const qty = parseInt(row.querySelector('input[name*="[quantity]"]')?.value || 0);

    if (partNumber && qty > 0) {
        hasValidPart = true;
        break;
    }
}

if (!hasValidPart) {
    alert("Please add at least one valid part with a part number and quantity.");
    return;
}



    calculateGrandTotal();
    
    const content = document.getElementById('confirmation-content');

    const customer_name = document.getElementById('customer_name').value.trim();
    const contact_number = document.getElementById('contact_number').value.trim();
    const vehicle_number = document.getElementById('vehicle_number').value.trim();
    const date = document.getElementById('date').value;
    const grand_total = parseFloat(document.getElementById('grand_total').value || 0);

    const vehicle_model = document.querySelector('[name="vehicle_model"]').value.trim();
    const odo_value = document.querySelector('[name="odo_value"]').value.trim();
    const odo_type = document.querySelector('[name="odo_type"]').value.trim();
    const make = document.querySelector('[name="make"]').value.trim();


        // Validate required fields
        if (!customer_name || !contact_number || !vehicle_number || !date || !vehicle_model || !odo_value || !make ) {
        alert("Please fill in all required fields:\n- Contact Number\n- Customer Name\n- Vehicle Number\n- Date\n- Vehicle model\n- make \n- ODO");
        return;
    }


 let html = `
     <div class="container-fluid p-0">
        <div class="d-flex header">
            <img src="../images/black.png" class="position-absolute img-fluid w-50" />
            <img src="../images/red.png" class="position-absolute img-fluid w-50 end-0" />
            <img src="../images/logo.webp" class="position-absolute start-0 mt-3 ms-5" style="height: 30px;" />
            <h4 class="position-absolute end-0 mt-3 me-5 text-light fw-bold">INVOICE</h4>
        </div>
    </div>
<div class="row mb-4">
  <div class="col-md-6">
    <div class="customer-info">
      <p><strong>Invoice No:</strong> ${generatedInvoiceNo}</p>
      <p><strong>Customer Name:</strong> ${customer_name}</p>
      <p><strong>Contact Number:</strong> ${contact_number}</p>
       <p><strong>Date:</strong> ${date}</p>
    </div>
  </div>
  <div class="col-md-6">
    <div class="vehicle-info">
     <p><strong>Vehicle Number:</strong> ${vehicle_number}</p>
      <p><strong>Make:</strong> ${make}</p>
      <p><strong>Vehicle Model:</strong> ${vehicle_model}</p>
      <p><strong>DTO:</strong> ${odo_value} ${odo_type ? '(' + odo_type + ')' : ''}</p>
     
    </div>
  </div>
</div>
`;


// In the Sold Parts table section
html += `

<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr class="sold-parts-header">
            <th>Part No</th>
            <th>Part Name</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Discount (%)</th>
            <th>Discount Amount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
`;


document.querySelectorAll('#parts-table tbody tr').forEach(row => {
    const partNo = row.querySelector('input[name*="[part_number]"]')?.value || '';
    const partName = row.querySelector('input[name*="[part_name]"]')?.value || '';

    const qtyField = row.querySelector('input[name*="[qty]"]') ?? row.cells[2]?.querySelector('input');
    const unitPriceField = row.querySelector('input[name*="[unit_price]"]') ?? row.cells[3]?.querySelector('input');
    const discountField = row.querySelector('input[name*="[discount]"]') ?? row.cells[4]?.querySelector('input');
    const totalField = row.querySelector('input[name*="[total]"]') ?? row.cells[6]?.querySelector('input');

    const qty = parseFloat(qtyField?.value || 0);
    const unitPrice = parseFloat(unitPriceField?.value || 0);
    const discount = parseFloat(discountField?.value || 0);
    const total = parseFloat(totalField?.value || 0);
    const discountAmount = (qty * unitPrice * discount) / 100;

    html += `
        <tr>
            <td>${partNo}</td>
            <td>${partName}</td>
            <td>${qty}</td>
            <td>${unitPrice.toFixed(2)}</td>
            <td>${discount.toFixed(2)}%</td>
            <td>${discountAmount.toFixed(2)}</td>
            <td>${total.toFixed(2)}</td>
        </tr>
    `;
});

html += `</tbody></table>`;

// In the Other Costs table section

html += `
<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr class="other-costs-header">
            <th>Description</th>
            <th>Price</th>
            <th>Discount (%)</th>
            <th>Discount Amount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
`;

document.querySelectorAll('#costs-table tbody tr').forEach(row => {
    const desc = row.querySelector('input[name*="[description]"]')?.value || '';
    const price = parseFloat(row.querySelector('input[name*="[price]"]')?.value || 0).toFixed(2);
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]')?.value || 0).toFixed(2);
    const discountAmount = parseFloat(row.querySelector('input[name*="[discount_amount]"]')?.value || 0).toFixed(2);
    const total = parseFloat(row.querySelector('input[name*="[total]"]')?.value || 0).toFixed(2);

    html += `
        <tr>
            <td>${desc}</td>
            <td>${price}</td>
            <td>${discount}%</td>
            <td>${discountAmount}</td>
            <td>${total}</td>
        </tr>
    `;
});

html += `</tbody></table>`;



    // Grand Total
    html += `<p><strong>Grand Total:</strong> Rs. ${grand_total.toFixed(2)}</p>`;

    content.innerHTML = html;
       document.getElementById('confirmation-popup').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}


    function hidePopup() {
        document.getElementById('confirmation-popup').style.display = 'none';
    document.body.style.overflow = 'auto'; 
    }

     function downloadConfirmation() {
    // Create a new div for PDF generation
    const pdfContainer = document.createElement('div');
    pdfContainer.style.fontFamily = "'Nunito Sans', sans-serif";
    pdfContainer.style.padding = "20px";
    pdfContainer.style.width = "100%";
    
    // Create a simple table structure that html2pdf can handle reliably
    let htmlContent = `
        <div style="width: 100%; background: #ffffff; padding: 20px; box-sizing: border-box;">
            <!-- Header -->
            <div style="background: #000; height: 80px; position: relative; margin-bottom: 30px;">
                <div style="position: absolute; right: 0; width: 50%; height: 100%; background: #ED1D26;"></div>
                <div style="position: absolute; left: 30px; top: 20px;">
                    <img src="${window.location.origin}/images/logo.webp" style="height: 40px;">
                </div>
                <div style="position: absolute; right: 30px; top: 20px; color: white; font-weight: bold; font-size: 24px;">INVOICE</div>
            </div>
            
            <!-- Customer Details -->
            <div style="display: flex; margin-bottom: 20px;">
                <div style="flex: 1; padding-right: 20px;">
                    <div><strong>Customer Name:</strong> ${document.getElementById('customer_name').value}</div>
                    <div><strong>Contact Number:</strong> ${document.getElementById('contact_number').value}</div>
                    <div><strong>Vehicle Number:</strong> ${document.getElementById('vehicle_number').value}</div>
                </div>
                <div style="flex: 1;">
                    <div><strong>Invoice No:</strong> ${document.getElementById('invoice_no_hidden').value}</div>
                    <div><strong>Date:</strong> ${document.getElementById('date').value}</div>
                    <div><strong>Make/Model:</strong> ${document.querySelector('[name="make"]').value} ${document.querySelector('[name="vehicle_model"]').value}</div>
                </div>
            </div>
            
            <!-- Parts Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background-color: #ED1D26; color: white;">
                        <th style="padding: 10px; text-align: left; width: 15%;">Part No</th>
                        <th style="padding: 10px; text-align: left; width: 25%;">Part Name</th>
                        <th style="padding: 10px; text-align: right; width: 12%;">Price</th>
                        <th style="padding: 10px; text-align: center; width: 8%;">Qty</th>
                        <th style="padding: 10px; text-align: right; width: 12%;">Discount</th>
                        <th style="padding: 10px; text-align: right; width: 14%;">Dsc. Price</th>
                        <th style="padding: 10px; text-align: right; width: 14%;">Total</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Add parts rows
    document.querySelectorAll('#parts-table tbody tr').forEach((row, index) => {
        const partNo = row.querySelector('input[name*="[part_number]"]')?.value || '';
        const partName = row.querySelector('input[name*="[part_name]"]')?.value || '';
        const qty = parseFloat(row.querySelector('input[name*="[quantity]"]')?.value || 0);
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]')?.value || 0);
        const discount = parseFloat(row.querySelector('input[name*="[discount]"]')?.value || 0);
        const discountAmount = (qty * unitPrice * discount) / 100;
        const total = (qty * unitPrice) - discountAmount;

        htmlContent += `
            <tr style="background-color: ${index % 2 === 0 ? '#f8f8f8' : '#ffffff'};">
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: left;">${partNo}</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: left;">${partName}</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${unitPrice.toFixed(2)}</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">${qty}</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${discount.toFixed(2)}%</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${discountAmount.toFixed(2)}</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${total.toFixed(2)}</td>
            </tr>
        `;
    });

    htmlContent += `</tbody></table>`;

    // Add other costs if they exist
    const otherCostsRows = document.querySelectorAll('#costs-table tbody tr');
    if (otherCostsRows.length > 0) {
        htmlContent += `
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background-color: #231F20; color: white;">
                        <th style="padding: 10px; text-align: left; width: 40%;">Description</th>
                        <th style="padding: 10px; text-align: right; width: 15%;">Price</th>
                        <th style="padding: 10px; text-align: right; width: 15%;">Discount</th>
                        <th style="padding: 10px; text-align: right; width: 15%;">Dsc. Price</th>
                        <th style="padding: 10px; text-align: right; width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
        `;

        otherCostsRows.forEach((row, index) => {
            const desc = row.querySelector('input[name*="[description]"]')?.value || '';
            const price = parseFloat(row.querySelector('input[name*="[price]"]')?.value || 0);
            const discount = parseFloat(row.querySelector('input[name*="[discount]"]')?.value || 0);
            const discountAmount = (price * discount) / 100;
            const total = price - discountAmount;

            htmlContent += `
                <tr style="background-color: ${index % 2 === 0 ? '#e8e8e8' : '#f0f0f0'};">
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: left;">${desc}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${price.toFixed(2)}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${discount.toFixed(2)}%</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${discountAmount.toFixed(2)}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">${total.toFixed(2)}</td>
                </tr>
            `;
        });

        htmlContent += `</tbody></table>`;
    }

    // Add grand total
    const grandTotal = parseFloat(document.getElementById('grand_total').value || 0).toFixed(2);
    htmlContent += `
        <div style="text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px;">
            Grand Total: Rs. ${grandTotal}
        </div>
    `;

    pdfContainer.innerHTML = htmlContent;

    // Generate PDF with optimal settings
    const opt = {
        margin: 10,
        filename: `Invoice_${document.getElementById('invoice_no_hidden').value}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            logging: false,
            useCORS: true,
            letterRendering: true
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        }
    };

    // Generate and save PDF
    html2pdf().set(opt).from(pdfContainer).save();
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
                                document.getElementById('vehicle_number').value = item.vehicle_number || '';
                                document.getElementById('vehicle_model').value = item.model || '';
                                document.getElementById('make').value = item.make || '';
                             
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
                                    document.getElementById('vehicle_model').value = item.vehicle_model;
                                    document.getElementById('make').value = item.vehicle_make;
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
                            // Trigger calculation of total and discount amount
                            calculateCostTotal(row.querySelector('input[name*="[price]"]'));
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



$(document).ready(function () {
    $('#vehicle_model').on('input', function () {
        let query = $(this).val();
        if (query.length >= 2) {
            $.ajax({
                url: '/get-vehicle-models',
                method: 'GET',
                data: { query: query },
                success: function (data) {
                    let html = '<div class="suggestion-list">';
                    data.forEach(item => {
                        html += `<div class="suggestion-item" data-model="${item.vehicle_model}" data-make="${item.make}">${item.vehicle_model} (${item.make})</div>`;
                    });
                    html += '</div>';
                    $('#vehicle-suggestions').html(html);
                }
            });
        } else {
            $('#vehicle-suggestions').empty();
        }
    });

    $(document).on('click', '.suggestion-item', function () {
        $('#vehicle_model').val($(this).data('model'));
        $('#make').val($(this).data('make'));
        $('#vehicle-suggestions').empty();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('#vehicle_model, #vehicle-suggestions').length) {
            $('#vehicle-suggestions').empty();
        }
    });
});



</script>
@endsection

