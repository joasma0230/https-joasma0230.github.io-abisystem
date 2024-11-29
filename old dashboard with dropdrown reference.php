<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'users'); // Replace with your database credentials
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
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
$divisions_query = "SELECT * FROM divisions"; // Adjust table name if necessary
$divisions_result = $conn->query($divisions_query);

$departments_query = "SELECT * FROM departments"; // Adjust table name if necessary
$departments_result = $conn->query($departments_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .content-section {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        .table {
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Data Entry</a>
    </div>
</nav>

<!-- Content Section -->
<div class="container mt-4">
    <!-- Man Power List -->
    <div class="content-section">
        <h2>Man Power List</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#registerUserModal">Add Manpower</button>
        <table class="table table-striped">
            <thead class="table-primary">
                <tr>
                    <!-- <th>#</th> -->
                    <th>Name</th>
                    <th>Division</th>
                    <th>Group/Department/Branch/Team</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($manpower_list as $manpower): ?>
                <tr data-id="<?php echo $manpower['id']; ?>">
                    <!-- <td><?php echo $manpower['id']; ?></td> -->
                    <td><?php echo $manpower['name']; ?></td>
                    <td><?php echo $manpower['division']; ?></td>
                    <td><?php echo $manpower['group_department_branch_team']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $manpower['id']; ?>" data-bs-toggle="modal" data-bs-target="#editManpowerModal">Edit</button>
                        <a href="delete_manpower.php?id=<?php echo $manpower['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Adding Manpower -->
<div class="modal fade" id="registerUserModal" tabindex="-1" aria-labelledby="registerUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add_manpower.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerUserModalLabel">Register New Manpower</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="#" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="division" class="form-label">Division</label>
                        <select class="form-select" name="division" required>
                            <option value="">Select Division</option>
                            <option value="LEGAL">LEGAL</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <option value="CUSTOMER EXPERIENCE GROUP">CUSTOMER EXPERIENCE GROUP</option>
                            <option value="SALES AND MARKETING - SUPPORT">SALES AND MARKETING - SUPPORT</option>
                            <option value="RISK MANAGEMENT DIVISION">RISK MANAGEMENT DIVISION</option>
                            <option value="MOTOR CLAIMS DIVISION">MOTOR CLAIMS DIVISION</option>
                            <option value="NON-MOTOR CLAIMS DIVISION">NON-MOTOR CLAIMS DIVISION</option>
                            <option value="TECHNICAL DIVISION">TECHNICAL DIVISION</option>
                            <option value="TECHNICAL AND TRAINING CENTER">TECHNICAL AND TRAINING CENTER</option>
                            <option value="FINANCE DIVISION">FINANCE DIVISION</option>
                            <option value="ADMINISTRATION DIVISION">ADMINISTRATION DIVISION</option>
                            <option value="TECHNOLOGY GROUP">TECHNOLOGY GROUP</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <?php while ($row = $divisions_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['division_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="group_department_branch_team" class="form-label">Group/Department/Branch/Team</label>
                        <select class="form-select" name="group_department_branch_team" required>
                                     <option value="">Select Group/Department/Branch/Team</option>
                                    <option value="EXECUTIVE OFFICE">EXECUTIVE OFFICE</option>
                                    <option value="SPECIAL TEAM">SPECIAL TEAM</option>
                                    <option value="LEGAL DEPARTMENT">LEGAL DEPARTMENT</option>
                                    <option value="INTERNET SALES UNIT">INTERNET SALES UNIT</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 0">CORPORATE SALES GROUP - TEAM 0</option>
                                    <option value="CORPORATE SALES COLLECTION AND RECEIPTING TEAM">CORPORATE SALES COLLECTION AND RECEIPTING TEAM</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 1">CORPORATE SALES GROUP - TEAM 1</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 2">CORPORATE SALES GROUP - TEAM 2</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 3">CORPORATE SALES GROUP - TEAM 3</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 4">CORPORATE SALES GROUP - TEAM 4</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 5">CORPORATE SALES GROUP - TEAM 5</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 6">CORPORATE SALES GROUP - TEAM 6</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 7">CORPORATE SALES GROUP - TEAM 7</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 8">CORPORATE SALES GROUP - TEAM 8</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 9">CORPORATE SALES GROUP - TEAM 9</option>
                                    <option value="CORPORATE SALES GROUP - TEAM 10">CORPORATE SALES GROUP - TEAM 10</option>
                                    <option value="CUSTOMER EXPERIENCE GROUP">CUSTOMER EXPERIENCE GROUP</option>
                                    <option value="CALL CENTER GROUP">CALL CENTER GROUP</option>
                                    <option value="DIGITAL MARKETING">DIGITAL MARKETING</option>
                                    <option value="BRANCHES SALES AND SUPPORT GROUP">BRANCHES SALES AND SUPPORT GROUP</option>
                                    <option value="BRANCHES OPERATIONS SUPPORT DEPARTMENT">BRANCHES OPERATIONS SUPPORT DEPARTMENT</option>
                                    <option value="MARKETING & CORPORATE COMMUNICATION DEPARTMENT">MARKETING & CORPORATE COMMUNICATION DEPARTMENT</option>
                                    <option value="DEARBORN GROUP">DEARBORN GROUP</option>
                                    <option value="RISK MANAGEMENT DEPARTMENT">RISK MANAGEMENT DEPARTMENT</option>
                                    <option value="MOTOR CLAIMS GROUP">MOTOR CLAIMS GROUP</option>
                                    <option value="NON-MOTOR CLAIMS GROUP">NON-MOTOR CLAIMS GROUP</option>
                                    <option value="TECHNICAL DIVISION">TECHNICAL DIVISION</option>
                                    <option value="ACAA PHILIPPINES">ACAA PHILIPPINES</option>
                                    <option value="TECHNICAL AND TRAINING CENTER">TECHNICAL AND TRAINING CENTER</option>
                                    <option value="CHIEF FINANCIAL OFFICER'S OFFICE">CHIEF FINANCIAL OFFICER'S OFFICE</option>
                                    <option value="CORPORATE FINANCE GROUP">CORPORATE FINANCE GROUP</option>
                                    <option value="REALTY DEPARTMENT">REALTY DEPARTMENT</option>
                                    <option value="FINANCE OPERATIONS GROUP">FINANCE OPERATIONS GROUP</option>
                                    <option value="GENERAL SERVICES DEPARTMENT">GENERAL SERVICES DEPARTMENT</option>
                                    <option value="HUMAN RESOURCE MANAGEMENT DEPARTMENT">HUMAN RESOURCE MANAGEMENT DEPARTMENT</option>
                                    <option value="HOF SPECIAL SERVICES">HOF SPECIAL SERVICES</option>
                                    <option value="INTERNAL AUDIT DEPARTMENT">INTERNAL AUDIT DEPARTMENT</option>
                                    <option value="TREASURY DEPARTMENT">TREASURY DEPARTMENT</option>
                                    <option value="INFRA & CYBER SECURITY">INFRA & CYBER SECURITY</option>
                                    <option value="SYSTEMS MANAGEMENT">SYSTEMS MANAGEMENT</option>
                                    <option value="SYSTEMS DEVELOPMENT - TEAM 1">SYSTEMS DEVELOPMENT - TEAM 1</option>
                                    <option value="SYSTEMS DEVELOPMENT - TEAM 2">SYSTEMS DEVELOPMENT - TEAM 2</option>
                                    <option value="ALABANG BRANCH">ALABANG BRANCH</option>
                                    <option value="STA. ROSA EXTENSION OFFICE">STA. ROSA EXTENSION OFFICE</option>
                                    <option value="SUCAT EXTENSION OFFICE">SUCAT EXTENSION OFFICE</option>
                                    <option value="CALOOCAN BRANCH">CALOOCAN BRANCH</option>
                                    <option value="GLOBAL CITY BRANCH">GLOBAL CITY BRANCH</option>
                                    <option value="SHAW BOULEVARD BRANCH">SHAW BOULEVARD BRANCH</option>
                                    <option value="MANILA BRANCH">MANILA BRANCH</option>
                                    <option value="NOVALICHES EXTENSION OFFICE">NOVALICHES EXTENSION OFFICE</option>
                                    <option value="MAKATI BRANCH">MAKATI BRANCH</option>
                                    <option value="ORTIGAS BRANCH">ORTIGAS BRANCH</option>
                                    <option value="ANTIPOLO EXTENSION OFFICE">ANTIPOLO EXTENSION OFFICE</option>
                                    <option value="QUEZON CITY BRANCH">QUEZON CITY BRANCH</option>
                                    <option value="BAGUIO BRANCH">BAGUIO BRANCH</option>
                                    <option value="BONTOC EXTENSION OFFICE">BONTOC EXTENSION OFFICE</option>
                                    <option value="BATANGAS BRANCH">BATANGAS BRANCH</option>
                                    <option value="CABANATUAN BRANCH">CABANATUAN BRANCH</option>
                                    <option value="CAVITE BRANCH">CAVITE BRANCH</option>
                                    <option value="GENERAL TRIAS SALES OFFICE">GENERAL TRIAS SALES OFFICE</option>
                                    <option value="CARMONA BRANCH">CARMONA BRANCH</option>
                                    <option value="LAOAG BRANCH">LAOAG BRANCH</option>
                                    <option value="LEGASPI BRANCH">LEGASPI BRANCH</option>
                                    <option value="LUCENA BRANCH">LUCENA BRANCH</option>
                                    <option value="MALOLOS BRANCH">MALOLOS BRANCH</option>
                                    <option value="MARILAO EXTENSION OFFICE">MARILAO EXTENSION OFFICE</option>
                                    <option value="NAGA BRANCH">NAGA BRANCH</option>
                                    <option value="DAET SALES OFFICE">DAET SALES OFFICE</option>
                                    <option value="OLONGAPO BRANCH">OLONGAPO BRANCH</option>
                                    <option value="BALANGA EXTENSION OFFICE">BALANGA EXTENSION OFFICE</option>
                                    <option value="PALAWAN BRANCH">PALAWAN BRANCH</option>
                                    <option value="PANGASINAN BRANCH">PANGASINAN BRANCH</option>
                                    <option value="URDANETA EXTENSION OFFICE">URDANETA EXTENSION OFFICE</option>
                                    <option value="SANTIAGO BRANCH">SANTIAGO BRANCH</option>
                                    <option value="CAUAYAN EXTENSION OFFICE">CAUAYAN EXTENSION OFFICE</option>
                                    <option value="SAN FERNANDO LA UNION BRANCH">SAN FERNANDO LA UNION BRANCH</option>
                                    <option value="SAN FERNANDO PAMPANGA BRANCH">SAN FERNANDO PAMPANGA BRANCH</option>
                                    <option value="SAN PABLO LAGUNA BRANCH">SAN PABLO LAGUNA BRANCH</option>
                                    <option value="CALAMBA EXTENSION OFFICE">CALAMBA EXTENSION OFFICE</option>
                                    <option value="TARLAC BRANCH">TARLAC BRANCH</option>
                                    <option value="TUGUEGARAO BRANCH">TUGUEGARAO BRANCH</option>
                                    <option value="ANTIQUE BRANCH">ANTIQUE BRANCH</option>
                                    <option value="BACOLOD BRANCH">BACOLOD BRANCH</option>
                                    <option value="CEBU BRANCH">CEBU BRANCH</option>
                                    <option value="TALISAY EXTENSION OFFICE">TALISAY EXTENSION OFFICE</option>
                                    <option value="BOHOL BRANCH">BOHOL BRANCH</option>
                                    <option value="DUMAGUETE BRANCH">DUMAGUETE BRANCH</option>
                                    <option value="ILOILO BRANCH">ILOILO BRANCH</option>
                                    <option value="KALIBO BRANCH">KALIBO BRANCH</option>
                                    <option value="KALIBO - GRANDCARS">KALIBO - GRANDCARS</option>
                                    <option value="ORMOC BRANCH">ORMOC BRANCH</option>
                                    <option value="TACLOBAN EXTENSION OFFICE">TACLOBAN EXTENSION OFFICE</option>
                                    <option value="ROXAS BRANCH">ROXAS BRANCH</option>
                                    <option value="BUTUAN BRANCH">BUTUAN BRANCH</option>
                                    <option value="CAGAYAN DE ORO BRANCH">CAGAYAN DE ORO BRANCH</option>
                                    <option value="VALENCIA EXTENSION OFFICE">VALENCIA EXTENSION OFFICE</option>
                                    <option value="DAVAO BRANCH">DAVAO BRANCH</option>
                                    <option value="TAGUM EXTENSION OFFICE">TAGUM EXTENSION OFFICE</option>
                                    <option value="GENERAL SANTOS BRANCH">GENERAL SANTOS BRANCH</option>
                                    <option value="KIDAPAWAN EXTENSION OFFICE">KIDAPAWAN EXTENSION OFFICE</option>
                                    <option value="ILIGAN BRANCH">ILIGAN BRANCH</option>
                                    <option value="PAGADIAN BRANCH">PAGADIAN BRANCH</option>
                                    <option value="DIPOLOG EXTENSION OFFICE">DIPOLOG EXTENSION OFFICE</option>
                                    <option value="SURIGAO BRANCH">SURIGAO BRANCH</option>
                                    <option value="ZAMBOANGA BRANCH">ZAMBOANGA BRANCH</option>
                            <?php while ($row = $departments_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['department_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Editing Manpower -->
<div class="modal fade" id="editManpowerModal" tabindex="-1" aria-labelledby="editManpowerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_manpower.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editManpowerModalLabel">Edit Manpower</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" id="edit-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-division" class="form-label">Division</label>
                        <select class="form-select" name="division" id="edit-division" required>
                            <option value="please choose">Select Division</option>
                            <option value="1">EXECUTIVE OFFICE</option>
                            <option value="LEGAL">LEGAL</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <option value="CUSTOMER EXPERIENCE GROUP">CUSTOMER EXPERIENCE GROUP</option>
                            <option value="SALES AND MARKETING - SUPPORT">SALES AND MARKETING - SUPPORT</option>
                            <option value="RISK MANAGEMENT DIVISION">RISK MANAGEMENT DIVISION</option>
                            <option value="MOTOR CLAIMS DIVISION">MOTOR CLAIMS DIVISION</option>
                            <option value="NON-MOTOR CLAIMS DIVISION">NON-MOTOR CLAIMS DIVISION</option>
                            <option value="TECHNICAL DIVISION">TECHNICAL DIVISION</option>
                            <option value="TECHNICAL AND TRAINING CENTER">TECHNICAL AND TRAINING CENTER</option>
                            <option value="FINANCE DIVISION">FINANCE DIVISION</option>
                            <option value="ADMINISTRATION DIVISION">ADMINISTRATION DIVISION</option>
                            <option value="TECHNOLOGY GROUP">TECHNOLOGY GROUP</option>
                            <option value="SALES AND MARKETING - DIRECT">SALES AND MARKETING - DIRECT</option>
                            <?php while ($row = $divisions_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['division_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-group_department_branch_team" class="form-label">Group/Department/Branch/Team</label>
                        <select class="form-select" name="group_department_branch_team" id="edit-group_department_branch_team" required>
                            <option value="">Select Group/Department/Branch/Team</option>
                            <?php while ($row = $departments_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['department_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Populate the edit modal with existing data when clicking "Edit"
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).closest('tr').find('td:nth-child(2)').text();
        const division = $(this).closest('tr').find('td:nth-child(3)').text();
        const group_department_branch_team = $(this).closest('tr').find('td:nth-child(4)').text();

        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $('#edit-division').val(division);
        $('#edit-group_department_branch_team').val(group_department_branch_team);
    });
</script>

</body>
</html>
