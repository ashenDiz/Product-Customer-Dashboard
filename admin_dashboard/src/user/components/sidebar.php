<div class="flex w-64 h-screen bg-gray-800 text-white flex-col">
    <div class="p-4 text-xl font-bold">Menu</div>
    <ul class="flex-grow space-y-4">
        <li>
            <a href="customerDashboard.php?page=customer_management" class="block py-2 px-4 hover:bg-gray-700">Customer</a>
        </li>
        <li>
            <a href="customerDashboard.php?page=product_management" class="block py-2 px-4 hover:bg-gray-700">Products</a>
        </li>
        <li>
            <a href="customerDashboard.php?page=invoice" class="block py-2 px-4 hover:bg-gray-700">Create Invoice</a>
        </li>
        <li>
            <a href="#" class="block py-2 px-4 hover:bg-gray-700">Settings</a>
        </li>
    </ul>

    <!-- Logout button positioned at the bottom center -->
    <div class="mt-auto mb-4 flex justify-center w-full">
        <button onclick="window.location.href='../php/logout.php'"
                class="bg-white text-lg font-semibold text-black border-2 border-black py-2 px-5 rounded-sm hover:border-green-500 hover:bg-green-500 hover:text-white">
                Logout
        </button>
    </div>
</div>
