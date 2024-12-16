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
<!-- src\user\screens\product_management.php -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="p-8">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold underline">Product Management</h2>
        <button id="addProductBtn" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-500">Product Form</button>
    </div>

    <!-- Add Product Form -->
    <div id="addProductForm" class="mb-8 hidden">
        <h3 class="text-xl text-blue-900 font-semibold mb-2">Add New Product</h3>
        <form id="productForm" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-1" for="productName">Product Name</label>
                <input type="text" id="productName" class="w-full border border-gray-300 rounded p-2" placeholder="Enter product name" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-1" for="price">Price</label>
                <input type="number" id="price" class="w-full border border-gray-300 rounded p-2" placeholder="Enter price" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-1" for="quantity">Quantity</label>
                <input type="number" id="quantity" class="w-full border border-gray-300 rounded p-2" placeholder="Enter quantity" required>
            </div>
            <button type="submit" class="bg-green-600 font-semibold text-white py-2 px-6 rounded-lg hover:bg-green-500">Submit</button>
        </form>
    </div>

    <!-- Product Table -->
    <div id="productTable">
        <h3 class="text-lg font-semibold mb-2">All Products</h3>
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">ID</th>
                    <th class="border border-gray-300 px-4 py-2">Product Name</th>
                    <th class="border border-gray-300 px-4 py-2">Price</th>
                    <th class="border border-gray-300 px-4 py-2">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody id="productList">
                <!-- Products will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addProductBtn = document.getElementById('addProductBtn');
            const addProductForm = document.getElementById('addProductForm');
            const productForm = document.getElementById('productForm');
            let isEditMode = false;
            let editProductId = null;

            addProductBtn.addEventListener('click', () => {
                addProductForm.classList.toggle('hidden');
                productForm.reset();
                isEditMode = false;
                editProductId = null;
            });

            const fetchProducts = () => {
                fetch('../php/productController.php')
                    .then(response => response.json())
                    .then(data => {
                        const productList = document.getElementById('productList');
                        productList.innerHTML = '';
                        if (data.status === 'success' && Array.isArray(data.data)) {
                            data.data.forEach(product => {
                                productList.innerHTML += `
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">${product.id}</td>
                                        <td class="border border-gray-300 px-4 py-2">${product.productName}</td>
                                        <td class="border border-gray-300 px-4 py-2">${product.price}</td>
                                        <td class="border border-gray-300 px-4 py-2">${product.quantity}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <button class="bg-yellow-500 text-white py-1 px-2 rounded hover:bg-yellow-600" onclick="editProduct(${product.id}, '${product.productName}', ${product.price}, ${product.quantity})">Update</button>
                                            <button class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600" onclick="deleteProduct(${product.id})">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            productList.innerHTML = `<tr><td colspan="5" class="border border-gray-300 px-4 py-2 text-center">No products found</td></tr>`;
                        }
                    })
                    .catch(err => console.error('Error fetching products:', err));
            };

            window.editProduct = (id, name, price, quantity) => {
                isEditMode = true;
                editProductId = id;
                addProductForm.classList.remove('hidden');
                document.getElementById('productName').value = name;
                document.getElementById('price').value = price;
                document.getElementById('quantity').value = quantity;
            };

            window.deleteProduct = (id) => {
                if (!confirm('Are you sure you want to delete this product?')) return;

                fetch('../php/productController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ delete: true, id: id })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Toastify({ text: data.message, backgroundColor: "green", duration: 3000 }).showToast();
                            fetchProducts();
                        } else {
                            Toastify({ text: 'Failed to delete product', backgroundColor: "red", duration: 3000 }).showToast();
                        }
                    })
                    .catch(err => console.error('Error deleting product:', err));
            };

            productForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const productName = document.getElementById('productName').value;
                const price = document.getElementById('price').value;
                const quantity = document.getElementById('quantity').value;

                const formData = new FormData();
                formData.append('productName', productName);
                formData.append('price', price);
                formData.append('quantity', quantity);
                if (isEditMode) {
                    formData.append('update', true);
                    formData.append('id', editProductId);
                }

                fetch('../php/productController.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Toastify({ text: data.message, backgroundColor: "green", duration: 3000 }).showToast();
                            addProductForm.classList.add('hidden');
                            fetchProducts();
                        } else {
                            Toastify({ text: data.message, backgroundColor: "red", duration: 3000 }).showToast();
                        }
                    })
                    .catch(err => console.error('Error submitting product:', err));
            });

            fetchProducts(); // Fetch products when page loads
        });
    </script>
</body>
</html>
