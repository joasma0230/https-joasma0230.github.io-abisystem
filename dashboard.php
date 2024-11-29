


<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// Fetch user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $division = $_POST['division'];
    $group_department_branch_team = $_POST['group_department_branch_team'];
    $position = $_POST['position'];
    $date_started = $_POST['date_started'];
    $training_title = $_POST['training_title'];
    $training_date = $_POST['training_date'];
    $training_venue = $_POST['training_venue'];
    $training_provider = $_POST['training_provider'];
    $training_type = $_POST['training_type'];
    $grade_rating = $_POST['grade_rating'];
    $remarks = $_POST['remarks'];

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO manpower (name, division, group_department_branch_team, position, date_started, training_title, training_date, training_venue, training_provider, training_type, grade_rating, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $name, $division, $group_department_branch_team, $position, $date_started, $training_title, $training_date, $training_venue, $training_provider, $training_type, $grade_rating, $remarks);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Manpower successfully added!";
    } else {
        $_SESSION['error_message'] = "Failed to add manpower.";
    }
    
    $stmt->close();
    $conn->close();
    $_SESSION['user_id'] = $user_id;

    // Redirect back to the dashboard page
    header("Location: dashboard.php");
    exit();
}




// Fetch manpower data
$query = "SELECT * FROM manpower";
$result = $conn->query($query);

$manpower_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $manpower_list[] = $row;
    }
}

// Fetch divisions and departments for dropdown options
$divisions_query = "SELECT * FROM divisions";
$divisions_result = $conn->query($divisions_query);

$departments_query = "SELECT * FROM departments";
$departments_result = $conn->query($departments_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manpower Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .content-section {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        /* Modal Scroll */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto; /* Vertical scroll for modal */
        }

        /* Scrollable form in modal */
        .form-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .form-group {
            flex: 1 1 calc(33.333% - 1rem); /* Three fields per row */
            margin-right: 10px;
        }

        /* Make the table horizontally scrollable */
        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
        }
    </style>
</head>
<body>

<!-- Navbar -->


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand" href="dashboard.php">Manpower Dashboard</a>

    <!-- Toggle Button for Mobile View -->
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNavDropdown"
      aria-controls="navbarNavDropdown"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible Content -->
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <!-- Search Form -->
      <form class="d-flex ms-auto" onsubmit="return false;">
    <input
        id="search-input"
        class="form-control me-2"
        type="search"
        placeholder="Search"
        aria-label="Search"
    />
    </form>

      <!-- Profile Dropdown -->
      <ul class="navbar-nav">
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <i class="bi bi-person-circle"></i> Profile
        </a>
        <ul
            class="dropdown-menu dropdown-menu-end"
            aria-labelledby="navbarDropdown"
        >
            <li>
                <a class="dropdown-item" href="profile.php">Edit Profile</a>
            </li>
            <li>
                <a class="dropdown-item" href="logout.php">Logout</a>
            </li>
        </ul>
    </li>
</ul>
    </div>
  </div>
</nav>




<!-- Content Section -->
<div class="container mt-4">
    <div class="content-section">
        <h2 class="mb-4">Man Power List</h2>
        <button
            class="btn btn-primary mb-3"
            data-bs-toggle="modal"
            data-bs-target="#registerUserModal"
        >
            Add Manpower
        </button>
        
        <!-- Table -->
        <div class="table-wrapper">
            <table
                id="manpower-table"
                class="table table-striped table-hover"
            >
                <thead class="table-light">
                    <tr>
                        <th>Employee Name</th>
                        <th>Division</th>
                        <th>Group/Department/Branch/Team</th>
                        <th>Position</th>
                        <th>Date Started</th>
                        <th>Training Title</th>
                        <th>Training Date</th>
                        <th>Training Venue</th>
                        <th>Training Provider</th>
                        <th>Training Type</th>
                        <th>Grade/Rating</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manpower_list as $manpower): ?>
                    <tr data-id="<?php echo $manpower['id']; ?>">
                        <td><?php echo $manpower['name']; ?></td>
                        <td><?php echo $manpower['division']; ?></td>
                        <td>
                            <?php echo $manpower['group_department_branch_team']; ?>
                        </td>
                        <td><?php echo $manpower['position']; ?></td>
                        <td><?php echo htmlspecialchars($manpower['date_started']); ?></td>
                        <td><?php echo $manpower['training_title']; ?></td>
                        <td><?php echo htmlspecialchars($manpower['training_date']); ?></td>
                        <td><?php echo $manpower['training_venue']; ?></td>
                        <td><?php echo $manpower['training_provider']; ?></td>
                        <td><?php echo $manpower['training_type']; ?></td>
                        <td><?php echo $manpower['grade_rating']; ?></td>
                        <td><?php echo $manpower['remarks']; ?></td>
                        <td>
                        <button
                                class="btn btn-sm btn-warning edit-btn"
                                data-id="<?php echo $manpower['id']; ?>"
                                data-name="<?php echo htmlspecialchars($manpower['name']); ?>"
                                data-division="<?php echo htmlspecialchars($manpower['division']); ?>"
                                data-group="<?php echo htmlspecialchars($manpower['group_department_branch_team']); ?>"
                                data-position="<?php echo htmlspecialchars($manpower['position']); ?>"
                                data-date-started="<?php echo htmlspecialchars($manpower['date_started']); ?>"
                                data-training-title="<?php echo htmlspecialchars($manpower['training_title']); ?>"
                                data-training-date="<?php echo htmlspecialchars($manpower['training_date']); ?>"
                                data-training-venue="<?php echo htmlspecialchars($manpower['training_venue']); ?>"
                                data-training-provider="<?php echo htmlspecialchars($manpower['training_provider']); ?>"
                                data-training-type="<?php echo htmlspecialchars($manpower['training_type']); ?>"
                                data-grade-rating="<?php echo htmlspecialchars($manpower['grade_rating']); ?>"
                                data-remarks="<?php echo htmlspecialchars($manpower['remarks']); ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#editManpowerModal"
>
                                        Edit
                        </button>

                            <a
                                href="delete_manpower.php?id=<?php echo $manpower['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirmDelete();"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                    </table>
        </div>
    </div>
</div>
<script>
    // Function to filter rows in the table
    function filterTable() {
        const query = document
            .getElementById("search-input")
            .value.toLowerCase(); // Get search input
        const rows = document.querySelectorAll(
            "#manpower-table tbody tr"
        ); // Get all table rows

        rows.forEach((row) => {
            const rowText = row.textContent.toLowerCase(); // Get row content
            row.style.display = rowText.includes(query) ? "" : "none"; // Hide/show rows
        });
    }

    // Event listeners for search functionality
    document
        .getElementById("search-input")
        .addEventListener("input", filterTable); // On typing
    document
        .getElementById("search-button")
        .addEventListener("click", filterTable); // On button click
</script>
<!-- Modal for Adding Manpower -->
<div class="modal fade" id="registerUserModal" tabindex="-1" aria-labelledby="registerUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add_manpower.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerUserModalLabel">Register New Manpower</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body form-wrapper">
                    <div class="form-group">
                        <label for="name" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="division" class="form-label">Division</label>
                        <select class="form-select" name="division" required>
                            <option value="">Select Division</option>
                            <option value="LEGAL">LEGAL</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <option value="CUSTOMER EXPERIENCE GROUP">CUSTOMER EXPERIENCE GROUP</option>
                            <option value="SALES AND MARKETING - SUPPORT">SALES AND MARKETING - SUPPORT</option>
                            <?php while ($row = $divisions_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['division_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="group_department_branch_team" class="form-label">Group/Department/Branch/Team</label>
                        <select class="form-select" name="group_department_branch_team" required>
                            <option value="">Select Group/Department/Branch/Team</option>
                            <option value="EXECUTIVE OFFICE">EXECUTIVE OFFICE</option>
                            <option value="SPECIAL TEAM">SPECIAL TEAM</option>
                            <option value="LEGAL DEPARTMENT">LEGAL DEPARTMENT</option>
                            <option value="INTERNET SALES UNIT">INTERNET SALES UNIT</option>
                            <?php while ($row = $departments_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['department_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                    <div class="form-group">
                        <label for="date_started" class="form-label">Date Started</label>
                        <input type="date" class="form-control" name="date_started" required>
                    </div>
                    <div class="form-group">
                        <label for="training_title" class="form-label">Training Title</label>
                        <input type="text" class="form-control" name="training_title" required>
                    </div>
                    <div class="form-group">
                        <label for="training_date" class="form-label">Training Date</label>
                        <input type="text" class="form-control" name="training_date" required>
                    </div>
                    <div class="form-group">
                        <label for="training_venue" class="form-label">Training Venue</label>
                        <input type="text" class="form-control" name="training_venue" required>
                    </div>
                    <div class="form-group">
                        <label for="training_provider" class="form-label">Training Provider</label>
                        <input type="text" class="form-control" name="training_provider" required>
                    </div>
                    <div class="form-group">
                        <label for="training_type" class="form-label">Training Type</label>
                        <input type="text" class="form-control" name="training_type" required>
                    </div>
                    <div class="form-group">
                        <label for="grade_rating" class="form-label">Grade/Rating</label>
                        <input type="text" class="form-control" name="grade_rating" required>
                    </div>
                    <div class="form-group">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="return confirmRegistration();">Register</button>
                <script>
function confirmRegistration() {
    return confirm("Are you sure you want to register this manpower?");
}
</script>
                    
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Editing Manpower -->
<div class="modal fade" id="editManpowerModal" tabindex="-1" aria-labelledby="editManpowerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_manpower.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editManpowerModalLabel">Edit Manpower</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body form-wrapper">
                    <!-- Edit form fields with pre-filled data (JavaScript will handle this) -->
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label for="edit-name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" id="edit-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-division" class="form-label">Division</label>
                        <select class="form-select" name="division" id="edit-division" required>
                            <option value="">Select Division</option>
                            <option value="LEGAL">LEGAL</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <option value="CUSTOMER EXPERIENCE GROUP">CUSTOMER EXPERIENCE GROUP</option>
                            <option value="SALES AND MARKETING - SUPPORT">SALES AND MARKETING - SUPPORT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-group_department_branch_team" class="form-label">Group/Department/Branch/Team</label>
                        <select class="form-select" name="group_department_branch_team" id="edit-group_department_branch_team" required>
                            <option value="">Select Group/Department/Branch/Team</option>
                            <option value="EXECUTIVE OFFICE">EXECUTIVE OFFICE</option>
                            <option value="SPECIAL TEAM">SPECIAL TEAM</option>
                            <option value="LEGAL DEPARTMENT">LEGAL DEPARTMENT</option>
                            <option value="INTERNET SALES UNIT">INTERNET SALES UNIT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-position" class="form-label">Position</label>
                        <input type="text" class="form-control" name="position" id="edit-position" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_started" class="form-label">Date Started</label>
                        <input type="text" class="form-control" name="date_started" id="edit-date_started" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-training_title" class="form-label">Training Title</label>
                        <input type="text" class="form-control" name="training_title" id="edit-training_title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-training_date" class="form-label">Training Date</label>
                        <input type="text" class="form-control" name="training_date" id="edit-training_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-training_venue" class="form-label">Training Venue</label>
                        <input type="text" class="form-control" name="training_venue" id="edit-training_venue" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-training_provider" class="form-label">Training Provider</label>
                        <input type="text" class="form-control" name="training_provider" id="edit-training_provider" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-training_type" class="form-label">Training Type</label>
                        <input type="text" class="form-control" name="training_type" id="edit-training_type" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-grade_rating" class="form-label">Grade/Rating</label>
                        <input type="text" class="form-control" name="grade_rating" id="edit-grade_rating" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="edit-remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >Save Changes</button>
                    <script>
        // JavaScript function to show confirmation dialog
        function confirmEdit(event, editUrl) {
            const confirmed = confirm("Are you sure you want to proceed with this edit?");
            if (confirmed) {
                // Redirect to the edit page
                window.location.href = editUrl;
            } else {
                // Prevent default action if not confirmed
                event.preventDefault();
            }
        }
    </script>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
// Handle editing of manpower data
document.querySelectorAll('.edit-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        // Get data from button attributes
        document.getElementById('edit-id').value = this.getAttribute('data-id');
        document.getElementById('edit-name').value = this.getAttribute('data-name');
        document.getElementById('edit-division').value = this.getAttribute('data-division');
        document.getElementById('edit-group_department_branch_team').value = this.getAttribute('data-group');
        document.getElementById('edit-position').value = this.getAttribute('data-position');
        document.getElementById('edit-date_started').value = this.getAttribute('data-date_started');
        document.getElementById('edit-training_title').value = this.getAttribute('data-training_title');
        document.getElementById('edit-training_date').value = this.getAttribute('data-training_date');
        document.getElementById('edit-training_venue').value = this.getAttribute('data-training_venue');
        document.getElementById('edit-training_provider').value = this.getAttribute('data-training_provider');
        document.getElementById('edit-training_type').value = this.getAttribute('data-training_type');
        document.getElementById('edit-grade_rating').value = this.getAttribute('data-grade_rating');
        document.getElementById('edit-remarks').value = this.getAttribute('data-remarks');
    });
});

// Attach confirmation to the form submission for editing
document.querySelector('#editManpowerModal form').addEventListener('submit', function(event) {
    // Show confirmation dialog before submitting the form
    const confirmed = confirm("Are you sure you want to proceed with this edit?");
    if (!confirmed) {
        // Prevent the form from submitting if user clicks Cancel
        event.preventDefault();
    }
});
</script>

</body>
</html>
