@extends('layouts.app')

@section('content')
<h2>Customer List</h2>

<input type="text" id="customer_search" placeholder="Search by name or number" autocomplete="off">

<div id="customerSuggestions" style="border: 1px solid #ccc; display: none; position: absolute; background: #fff; z-index: 10;"></div>
<button id="resetBtn" onclick="resetCustomerTable()" style="display: none;">Show All</button>

<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;" id="customer-table">
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Contact Number</th>
            <th>Registered Date</th>
            <th>Vehicles</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->customer_name }}</td>
            <td>{{ $customer->contact_number }}</td>
            <td>{{ $customer->created_at->format('Y-m-d') }}</td>
            <td>
                <ul>
                    @foreach($customer->vehicles as $vehicle)
                        <li>{{ $vehicle->vehicle_number }}</li>
                    @endforeach
                </ul>
            </td>
         <!--   <td>
                <button onclick="openEditModal('{{ $customer->contact_number }}')">Edit</button>
            </td> -->
        </tr>
        @endforeach
    </tbody>
</table>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById('customer_search');
    const suggestionBox = document.getElementById('customerSuggestions');

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length >= 2) {
            fetch(`/customers/search?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
                    if (data.length > 0) {
                        suggestionBox.style.display = 'block';
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.textContent = `${item.customer_name} (${item.contact_number})`;
                            div.style.padding = '5px';
                            div.style.cursor = 'pointer';

                            div.onclick = () => {
                            searchInput.value = `${item.customer_name} (${item.contact_number})`;
                            suggestionBox.innerHTML = '';
                            suggestionBox.style.display = 'none';

                            const contactNumber = item.contact_number;

                            document.querySelectorAll('#customer-table tbody tr').forEach(row => {
                                const cellText = row.querySelector('td:nth-child(2)')?.textContent?.trim();
                                if (cellText === contactNumber) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                            document.getElementById('resetBtn').style.display = 'inline-block';
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

    document.addEventListener('click', (e) => {
        if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
            suggestionBox.style.display = 'none';
        }
    });
});


function resetCustomerTable() {
    // Show all rows
    document.querySelectorAll('#customer-table tbody tr').forEach(row => {
        row.style.display = '';
    });

    // Clear search input
    document.getElementById('customer_search').value = '';

    // Hide reset button
    document.getElementById('resetBtn').style.display = 'none';
}


</script>



@endsection

<!-- Edit Modal -->
 <!--
<div id="editModal" style="display:none; position:fixed; top:10%; left:25%; width:50%; background:#fff; border:2px solid #444; padding:20px; z-index:9999;">
    <h3>Edit Customer</h3>
    <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_contact_number" name="contact_number">

        <label>Contact Number:</label><br>
        <input type="text" id="display_contact_number" disabled><br><br>

        <label>Customer Name:</label><br>
        <input type="text" name="customer_name" id="edit_customer_name" required><br><br>

        <button type="submit">Save</button>
        <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
</div> -->
<!--<script>
    function openEditModal(contactNumber) {
        fetch(`/customers/${contactNumber}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editForm').action = `/customers/${contactNumber}`;
                document.getElementById('edit_contact_number').value = data.contact_number;
                document.getElementById('display_contact_number').value = data.contact_number;
                document.getElementById('edit_customer_name').value = data.customer_name;
                document.getElementById('editModal').style.display = 'block';
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
-->
