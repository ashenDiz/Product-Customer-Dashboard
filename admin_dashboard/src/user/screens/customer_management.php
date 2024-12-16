<?php
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Prevent caching of this page to ensure no back navigation after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="p-8">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold underline">Customer Management</h2>
        <button id="addCustomerBtn" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-500">Customer Form</button>
    </div>

    <!-- Add Customer Form -->
    <div id="addCustomerForm" class="mb-8 hidden">
        <h3 id="formHeading" class="text-xl text-blue-900 font-semibold mb-2">Add New Customer</h3>
        <form id="customerForm" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-1" for="name">Name</label>
                <input type="text" id="name" class="w-full border border-gray-300 rounded p-2" placeholder="Enter name" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-1" for="email">Email</label>
                <input type="email" id="email" class="w-full border border-gray-300 rounded p-2" placeholder="Enter email" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-1" for="phone">Phone</label>
                <input type="text" id="phone" class="w-full border border-gray-300 rounded p-2" placeholder="Enter phone number" required>
            </div>
            <button type="submit" class="bg-green-600 font-semibold text-white py-2 px-6 rounded-lg hover:bg-green-500">Submit</button>
        </form>
    </div>

    <!-- Customer Table -->
    <div id="customerTable">
        <h3 class="text-lg font-semibold mb-2">All Customers</h3>
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">ID</th>
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Phone</th>
                    <th class="border border-gray-300 px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody id="customerList">
                <!-- Customers will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
            const addCustomerBtn = document.getElementById('addCustomerBtn');
            const addCustomerForm = document.getElementById('addCustomerForm');
            const customerForm = document.getElementById('customerForm');
            const formHeading = document.getElementById('formHeading'); // Get the heading element
            let isEditMode = false;
            let editCustomerId = null;

            // Toggle form visibility when Add Customer button is clicked
            addCustomerBtn.addEventListener('click', () => {
                addCustomerForm.classList.toggle('hidden');
                customerForm.reset(); // Clear form fields
                isEditMode = false; // Reset edit mode
                editCustomerId = null; // Clear edit ID
                formHeading.textContent = "Add New Customer"; // Reset heading to "Add New Customer"
            });

            const fetchCustomers = () => {
                fetch('../php/createcustomer.php')
                    .then(response => response.json())
                    .then(data => {
                        const customerList = document.getElementById('customerList');
                        customerList.innerHTML = '';
                        if (data.status === 'success' && Array.isArray(data.data)) {
                            data.data.forEach(customer => {
                                customerList.innerHTML += ` 
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">${customer.id}</td>
                                        <td class="border border-gray-300 px-4 py-2">${customer.name}</td>
                                        <td class="border border-gray-300 px-4 py-2">${customer.email}</td>
                                        <td class="border border-gray-300 px-4 py-2">${customer.phone}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <button class="bg-yellow-500 text-white py-1 px-2 rounded hover:bg-yellow-600" onclick="editCustomer(${customer.id}, '${customer.name}', '${customer.email}', '${customer.phone}')">Update</button>
                                            <button class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600" onclick="deleteCustomer(${customer.id})">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            customerList.innerHTML = `<tr><td colspan="5" class="border border-gray-300 px-4 py-2 text-center">No customers found</td></tr>`;
                        }
                    })
                    .catch(err => console.error('Error fetching customers:', err));
            };

            window.editCustomer = (id, name, email, phone) => {
                isEditMode = true;
                editCustomerId = id;
                addCustomerForm.classList.remove('hidden');
                formHeading.textContent = "Update Customer"; // Change the form heading to "Update Customer"
                document.getElementById('name').value = name;
                document.getElementById('email').value = email;
                document.getElementById('phone').value = phone;
            };

            window.deleteCustomer = (id) => {
                if (!confirm('Are you sure you want to delete this customer?')) return;

                fetch('../php/createcustomer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ delete: true, id: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Toastify({ text: data.message, backgroundColor: "green", duration: 3000 }).showToast();
                            fetchCustomers();
                        } else {
                            Toastify({ text: 'Failed to delete customer', backgroundColor: "red", duration: 3000 }).showToast();
                        }
                    })
                    .catch(err => console.error('Error deleting customer:', err));
            };

            // Handle form submission (create or update)
            customerForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;

                // Prepare the payload depending on whether the form is in edit mode or not
                const payload = isEditMode
                    ? { update: true, id: editCustomerId, name, email, phone } // If edit mode, include ID and update flag
                    : { name, email, phone }; // If add mode, just the new customer data

                fetch('../php/createcustomer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(payload) // Send data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Toastify({ text: data.message, backgroundColor: "green", duration: 3000 }).showToast();
                        fetchCustomers();  // Reload the customer list after successful operation
                        customerForm.reset();
                        addCustomerForm.classList.add('hidden');  // Hide form after submission
                        isEditMode = false;  // Reset edit mode flag
                        editCustomerId = null;  // Clear edit customer ID
                        formHeading.textContent = "Add New Customer"; // Reset heading back to "Add New Customer"
                    } else {
                        Toastify({ text: 'Failed to save customer', backgroundColor: "red", duration: 3000 }).showToast();
                    }
                })
                .catch(err => console.error('Error saving customer:', err));
            });

            // Initial load of customer data
            fetchCustomers();
        });
    </script>
</body>
</html>
