<?php
// src/collector/screens/collector_dashboard.php
require '../php/collector_dashboardcontroller.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Collector Dashboard</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <!-- Toast Notification -->
  <?php if (isset($_SESSION['toast'])): ?>
      <div id="toast" class="fixed top-20 right-4 bg-<?php echo $_SESSION['toast']['type'] === 'success' ? 'teal' : 'red'; ?>-600 text-white p-4 rounded shadow-lg">
          <p><?php echo $_SESSION['toast']['message']; ?></p>
      </div>
      <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>

  <header class="bg-green-600 p-4 flex justify-between items-center">
    <h1 class="text-white text-xl">E-Waste Management - Dashboard</h1>
    <a href="../php/logout.php" class="text-white bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</a>
  </header>
  
  <main class="p-6">
    <?php
      if (isset($_SESSION['error'])) {
          echo '<div class="mb-4 text-red-600">' . htmlspecialchars($_SESSION['error']) . '</div>';
          unset($_SESSION['error']);
      }

      if (isset($_SESSION['success'])) {
          echo '<div class="mb-4 text-green-600">' . htmlspecialchars($_SESSION['success']) . '</div>';
          unset($_SESSION['success']);
      }
    ?>

    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4">All E-Waste Disposal Requests</h2>

      <div class="flex justify-between items-center mb-4">
        <!-- Search Input -->
        <input type="text" id="search" placeholder="Search by user or location" class="border px-4 py-2 rounded w-1/3">
        
        <!-- Filter Dropdown -->
        <select id="statusFilter" class="border px-4 py-2 rounded">
          <option value="">Filter by Status</option>
          <option value="pending">Pending</option>
          <option value="accepted_by_you">Accepted by You</option>
          <option value="accepted">Taken by Others</option>
          <option value="completed">Completed</option>
        </select>
      </div>

      <!-- Table Section -->
      <?php if (count($requests) > 0): ?>
        <div class="overflow-x-auto">
          <table class="w-full table-auto">
            <thead>
              <tr class="bg-gray-200">
                <th class="px-4 py-2">User Name</th>
                <th class="px-4 py-2">Location</th>
                <th class="px-4 py-2">Waste Types</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
                <th class="px-4 py-2">Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($requests as $request): ?>
                  <tr class="text-center request-row" data-status="<?php echo htmlspecialchars($request['status']); ?>" data-collector="<?php echo ($request['collector_id'] === $collector_id) ? 'accepted_by_you' : 'accepted'; ?>">
                      <td class="border px-4 py-2"><?php echo htmlspecialchars($request['user_name']); ?></td>
                      <td class="border px-1 py-2 flex items-center">
                          <p class="text-sm text-gray-700 mr-2"><?php echo htmlspecialchars($request['location']); ?></p>
                          <button onclick="openGoogleMaps('<?php echo htmlspecialchars($request['location']); ?>')" class="bg-blue-500 text-white px-1 py-1 rounded hover:bg-blue-600">Map</button>
                      </td>

                      <td class="border px-4 py-2"><?php echo htmlspecialchars($request['waste_types']); ?></td>
                      <td class="border px-4 py-2 <?php 
                          if ($request['status'] === 'pending') {
                              echo 'text-white bg-yellow-500 font-semibold';
                          } elseif ($request['status'] === 'accepted') {
                              echo 'text-white bg-gray-400 font-semibold';
                          } elseif ($request['status'] === 'completed') {
                              echo 'text-white bg-gray-700 font-semibold';
                          }
                      ?>">
                          <?php 
                            echo ucfirst($request['status']); 
                            if ($request['status'] === 'accepted') {
                                if (isset($request['collector_id'])) {
                                    if ($request['collector_id'] === $collector_id) {
                                        echo ' <span class="text-black">by You</span>';
                                    } else {
                                        echo ' by another collector';
                                    }
                                } else {
                                    echo ' by another collector';
                                }
                            }
                          ?>
                      </td>
                      <td class="border px-4 py-2">
                          <?php if ($request['status'] === 'pending'): ?>
                            <form action="view_request_details.php" method="GET">
                                  <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                  <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 w-full">Accept</button>
                              </form>
                          <?php elseif ($request['status'] === 'accepted' && $request['collector_id'] === $collector_id): ?>
                              <form action="view_request_details.php" method="GET">
                                  <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                  <button type="submit" class="bg-teal-500 text-white px-3 py-1 rounded hover:bg-teal-600 w-full">View</button>
                              </form>
                          <?php elseif ($request['status'] === 'accepted'): ?>
                              <button class="bg-gray-500 text-white px-3 py-1 rounded w-full" disabled>Taken</button>
                          <?php elseif ($request['status'] === 'completed'): ?>
                              <button class="bg-gray-700 text-white px-3 py-1 rounded w-full" disabled>Completed</button>
                          <?php endif; ?>
                      </td>
                      <td class="border px-4 py-2"><?php echo htmlspecialchars($request['created_at']); ?></td>
                  </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-gray-700">No e-waste disposal requests found.</p>
      <?php endif; ?>
    </div>
  </main>
  <script>
    // Function to open Google Maps with the provided location and pin it
    function openGoogleMaps(location) {
        const coordinates = location.match(/Lat: ([\d.-]+), Lng: ([\d.-]+)/);
        if (coordinates) {
            const lat = coordinates[1];
            const lng = coordinates[2];
            const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`; // URL format to pin location
            window.open(mapsUrl, '_blank');
        } else {
            console.error('Invalid location format.');
        }
    }

    // JavaScript for search functionality
    document.getElementById('search').addEventListener('keyup', function() {
      const searchText = this.value.toLowerCase();
      const rows = document.querySelectorAll('.request-row');
      
      rows.forEach(function(row) {
        const userName = row.children[0].textContent.toLowerCase();
        const location = row.children[1].textContent.toLowerCase();

        if (userName.includes(searchText) || location.includes(searchText)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // JavaScript for filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
      const filterValue = this.value;
      const rows = document.querySelectorAll('.request-row');

      rows.forEach(function(row) {
        const status = row.getAttribute('data-status');
        const collectorId = row.getAttribute('data-collector');

        if (filterValue === '') {
          row.style.display = ''; // Show all rows
        } else if (filterValue === 'accepted_by_you' && collectorId === 'accepted_by_you') {
          row.style.display = ''; // Show rows accepted by you
        } else if (filterValue === 'accepted' && collectorId === 'accepted') {
          row.style.display = ''; // Show rows accepted by others
        } else if (filterValue === status && status !== 'accepted') {
          row.style.display = ''; // Show rows with matching statuses, excluding accepted
        } else {
          row.style.display = 'none'; // Hide rows that don't match the filter
        }

        // Additional logic to hide pending rows when filtering for "Accepted by You"
        if (filterValue === 'accepted_by_you' && status === 'pending') {
          row.style.display = 'none'; // Explicitly hide pending requests
        }
      });
    });
  </script>
  <script>
    // Automatically hide the toast after 3 seconds
    window.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 500); // Wait for fade-out before removing
            }, 3000); // 3 seconds
        }
    });
</script>
</body>
</html>
