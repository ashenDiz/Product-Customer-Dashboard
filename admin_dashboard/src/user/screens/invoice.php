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
    <title>Create Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="flex">
    <div class="flex-1 p-8">
        <h2 class="text-2xl font-bold mb-6">Create Invoice</h2>

        <!-- Customer Dropdown -->
        <label for="customerId" class="block text-lg">Select Customer</label>
        <select id="customerId" class="w-full border border-gray-300 p-2 rounded-md mb-6">
            <option value="">Select Customer</option>
        </select>

        <!-- Button to open Product Popup -->
        <button onclick="openProductPopup()" class="bg-blue-500 text-white py-2 px-4 rounded-md mb-6">Add Products</button>

        <!-- Invoice Table -->
        <h3 class="text-xl mb-4">Invoice Details</h3>
        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Product Name</th>
                    <th class="border border-gray-300 px-4 py-2">Price</th>
                    <th class="border border-gray-300 px-4 py-2">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2">Total</th>
                </tr>
            </thead>
            <tbody id="invoiceProducts"></tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="border border-gray-300 px-4 py-2 font-bold">Total Price</td>
                    <td id="totalPrice" class="border border-gray-300 px-4 py-2">0.00</td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-6">
            <button onclick="submitInvoice()" class="bg-green-500 text-white py-2 px-4 rounded-md">Create Invoice</button>
        </div>
    </div>

    <!-- Product Popup -->
    <div id="productPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded-md w-3/4 max-w-lg">
            <h3 class="text-xl mb-4">Select Products</h3>
            <input
                type="text"
                id="searchProduct"
                class="w-full border border-gray-300 p-2 rounded-md mb-4"
                placeholder="Search by product name or price..."
                onkeyup="filterProducts()"
            />
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Product Name</th>
                        <th class="border border-gray-300 px-4 py-2">Price</th>
                        <th class="border border-gray-300 px-4 py-2">Quantity</th>
                        <th class="border border-gray-300 px-4 py-2">Select</th>
                    </tr>
                </thead>
                <tbody id="productList"></tbody>
            </table>
            <div class="mt-4 flex justify-end">
                <button onclick="confirmSelectedProducts()" class="bg-green-500 text-white py-2 px-4 rounded-md mr-2">Confirm Products</button>
                <button onclick="closeProductPopup()" class="bg-red-500 text-white py-2 px-4 rounded-md">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Fetch customers
        const fetchCustomers = () => {
            fetch('../php/createcustomer.php')
                .then(response => response.json())
                .then(data => {
                    const customerDropdown = document.getElementById('customerId');
                    customerDropdown.innerHTML = '<option value="">-- Select a Customer --</option>';
                    data.data.forEach(customer => {
                        customerDropdown.innerHTML += `<option value="${customer.id}">${customer.name}</option>`;
                    });
                });
        };

        // Fetch products
        const fetchProducts = () => {
            fetch('../php/productController.php')
                .then(response => response.json())
                .then(data => {
                    const productList = document.getElementById('productList');
                    productList.innerHTML = '';
                    data.data.forEach(product => {
                        productList.innerHTML += `
                            <tr>
                                <td class="border px-4 py-2">${product.productName}</td>
                                <td class="border px-4 py-2">${product.price}</td>
                                <td class="border px-4 py-2">
                                    <input type="number" id="quantity_${product.id}" min="1" max="${product.quantity}" value="1" class="w-20 p-1">
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="checkbox" id="select_${product.id}" value="${product.id}">
                                </td>
                            </tr>`;
                    });
                });
        };

        // Open and close popup
        const openProductPopup = () => {
            fetchProducts();
            document.getElementById('productPopup').classList.remove('hidden');
        };
        const closeProductPopup = () => document.getElementById('productPopup').classList.add('hidden');

        // Add selected products to invoice
        const confirmSelectedProducts = () => {
            const rows = document.querySelectorAll('#productList tr');
            const invoiceProductsTable = document.getElementById('invoiceProducts');
            rows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox.checked) {
                    const productId = checkbox.value;
                    const productName = row.querySelector('td:nth-child(1)').innerText;
                    const price = parseFloat(row.querySelector('td:nth-child(2)').innerText);
                    const quantity = parseInt(document.getElementById(`quantity_${productId}`).value);
                    const total = price * quantity;

                    const newRow = document.createElement('tr');
                    newRow.dataset.productId = productId;
                    newRow.innerHTML = `
                        <td class="border px-4 py-2">${productName}</td>
                        <td class="border px-4 py-2">${price.toFixed(2)}</td>
                        <td class="border px-4 py-2">${quantity}</td>
                        <td class="border px-4 py-2">${total.toFixed(2)}</td>`;
                    invoiceProductsTable.appendChild(newRow);
                }
            });
            updateTotal();
            closeProductPopup();
        };

        // Update total price
        const updateTotal = () => {
            let total = 0;
            document.querySelectorAll('#invoiceProducts tr').forEach(row => {
                total += parseFloat(row.querySelector('td:last-child').innerText);
            });
            document.getElementById('totalPrice').innerText = total.toFixed(2);
        };

        // Filter products in the popup
        const filterProducts = () => {
            const searchValue = document.getElementById('searchProduct').value.toLowerCase();
            const rows = document.querySelectorAll('#productList tr');

            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
                const productPrice = row.querySelector('td:nth-child(2)').innerText.toLowerCase();

                if (productName.includes(searchValue) || productPrice.includes(searchValue)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        };

        // Submit invoice
        const submitInvoice = () => {
            const customerId = document.getElementById('customerId').value;
            const totalPrice = document.getElementById('totalPrice').innerText;
            const products = [];
            document.querySelectorAll('#invoiceProducts tr').forEach(row => {
                products.push({
                    productId: row.dataset.productId,
                    quantity: parseInt(row.querySelector('td:nth-child(3)').innerText)
                });
            });

            fetch('../php/invoice_controller.php', {
                method: 'POST',
                body: JSON.stringify({ customerId, totalPrice, products }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Toastify({ text: 'Invoice created successfully!', backgroundColor: 'green' }).showToast();
                } else {
                    Toastify({ text: data.message, backgroundColor: 'red' }).showToast();
                }
            });
        };

        // Onload
        window.onload = fetchCustomers;
    </script>
</body>
</html>
