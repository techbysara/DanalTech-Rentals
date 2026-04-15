<?php
session_start();
require_once '../config/database.php';

// User Management protection session for Admins only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add User Session
if (isset($_POST['addUserBtn'])) {
    $firstName      = trim($_POST['firstName']);
    $lastName       = trim($_POST['lastName']);
    $userEmail      = trim($_POST['userEmail']);
    $userPassword   = trim($_POST['userPassword']);
    $userRole       = trim($_POST['userRole']);

    // Verify if email already exists
    $emailCheckQuery    = "SELECT id FROM users WHERE email = ?";
    $emailCheckPrepared = $dbConn->prepare($emailCheckQuery);
    $emailCheckPrepared->bind_param("s", $userEmail);
    $emailCheckPrepared->execute();
    $emailCheckResult      = $emailCheckPrepared->get_result();

    if ($emailCheckResult->num_rows > 0) {
        header("Location: users.php?error=emailexists");
        exit();
    }


    // Password Hash Session 
    $hashedPassword   = password_hash($userPassword, PASSWORD_DEFAULT);

    $addUserQuery   = "INSERT INTO users (firstName, lastName, email, password, role) 
                    VALUES (?, ?, ?, ?, ?)";
    $addUserPrepared = $dbConn->prepare($addUserQuery);
    $addUserPrepared->bind_param(
        "sssss",
        $firstName,
        $lastName,
        $userEmail,
        $hashedPassword,
        $userRole
    );

    if ($addUserPrepared->execute()) {
    header("Location: users.php?success=useradded");
    exit();

    } else {
        header("Location: users.php?error=addfailed");
        exit();
    }

} 

// Edit User Session
if (isset($_POST['editUserBtn'])) {
    $editUserID   = intval($_POST['editUserID']);
    $firstName    = trim($_POST['firstName']);
    $lastName     = trim($_POST['lastName']);
    $userEmail    = trim($_POST['userEmail']);
    $userRole     = trim($_POST['userRole']);

    $editUserQuery    = "UPDATE users SET 
                        firstName = ?, lastName = ?, 
                        email = ?, role = ? 
                        WHERE id = ?";
    $editUserPrepared = $dbConn->prepare($editUserQuery);
    $editUserPrepared->bind_param(
        "ssssi",
        $firstName,
        $lastName,
        $userEmail,
        $userRole,
        $editUserID
    );

    if ($editUserPrepared->execute()) {
        header("Location: users.php?success=userupdated");
        exit();
    } else {
        header("Location: users.php?error=editfailed");
        exit();
    }
}

// Delete User Session
if (isset($_GET['delete'])) {
    $deleteUserID       = intval($_GET['delete']);

    // Prevent admin from deleting themselves
    if ($deleteUserID == $_SESSION['userID']) {
        header("Location: users.php?error=cannotdeleteyourself");
        exit();
    }

    $deleteUserQuery    = "DELETE FROM users WHERE id = ?";
    $deleteUserPrepared = $dbConn->prepare($deleteUserQuery);
    $deleteUserPrepared->bind_param("i", $deleteUserID);
    $deleteUserPrepared->execute();
    header("Location: users.php?success=userdeleted");
    exit();
}

// Retrieve all Users
$usersListQuery  = "SELECT * FROM users ORDER BY created_at DESC";
$usersListResult = $dbConn->query($usersListQuery);

// User Message Session
$userMessage     = "";
$userMessageType = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'useradded') {
        $userMessage     = "User added successfully!";
        $userMessageType = "success";

    } elseif ($_GET['success'] == 'userdeleted') {
        $userMessage     = "User deleted successfully!";
        $userMessageType = "success";

    } elseif ($_GET['success'] == 'userupdated') {
        $userMessage     = "User updated successfully!";
        $userMessageType = "success";
    }
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'emailexists') {
        $userMessage     = "Email is already registered.";
        $userMessageType = "error";
    } elseif ($_GET['error'] == 'addfailed') {
        $userMessage     = "Failed to add user. Please try again.";
        $userMessageType = "error";
    } elseif ($_GET['error'] == 'editfailed') {
        $userMessage     = "Failed to update user. Please check details and try again.";
        $userMessageType = "error";
    } elseif ($_GET['error'] == 'cannotdeleteyourself') {
        $userMessage     = "You cannot delete your own account!";
        $userMessageType = "error";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - User Management</title>

    <!-- Bootstrap 5 -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DanalTech Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">

        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-brand">
                <a href="../index.php" class="dtr-logo" style="justify-content:center; padding: 0 0 10px 0;">
                    <div class="logo-badge">
                        <span class="logo-d">D</span>
                        <span class="logo-t">T</span>
                        <span class="logo-r">R</span>
                    </div>
                    <div class="logo-text-block">
                        <span class="logo-name">DanalTech</span>
                        <span class="logo-sub">Rentals</span>
                    </div>
                </a>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link">
                    Dashboard
                </a>
                <a href="equipment.php" class="nav-link">
                    Equipment
                </a>
                <a href="users.php" class="nav-link active">
                    Users
                </a>
                <a href="rentals.php" class="nav-link">
                    Rentals
                </a>
                <a href="../logout.php" class="nav-link logout">
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">

            <!-- Top Bar -->
            <div class="admin-topbar">
                <h1>User Management</h1>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
                    <div class="admin-profile">
                        Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>!
                    </div>
                </div>
            </div>

            <!-- User Message -->
            <?php if (!empty($userMessage)) { ?>
                <div class="alert alert-<?php echo ($userMessageType == 'error') ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $userMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Add User Button -->
            <div class="mb-4">
                <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                    Add New User
                </button>
            </div>

            <!-- Users Table -->
            <div class="admin-table-card">
                <h2>Registered Users</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersListResult->num_rows > 0) { ?>
                            <?php while ($userRow = $usersListResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($userRow['firstName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($userRow['lastName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($userRow['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo htmlspecialchars(strtolower($userRow['role']), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($userRow['role'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $userRow['last_login'] 
                                        ? htmlspecialchars(date('d M Y', strtotime($userRow['last_login'])), ENT_QUOTES, 'UTF-8') 
                                            : 'Never'; ?></td>
                                    <td><?php echo date('d M Y', strtotime($userRow['created_at'])); ?></td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button type="button"
                                        class="btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-id="<?php echo $userRow['id']; ?>"
                                        data-firstname="<?php echo htmlspecialchars($userRow['firstName'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-lastname="<?php echo htmlspecialchars($userRow['lastName'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-email="<?php echo htmlspecialchars($userRow['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-role="<?php echo htmlspecialchars($userRow['role'], ENT_QUOTES, 'UTF-8'); ?>">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <?php if ($userRow['id'] != $_SESSION['userID']) { ?>
                                            <a href="users.php?delete=<?php echo $userRow['id']; ?>"
                                            class="btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </a>
                                        <?php } else { ?>
                                            <span class="text-muted" style="font-size:0.8rem;">You</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="empty-table">
                                    No users found!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD USER MODAL -->
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="users.php">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="firstName"
                                    placeholder="Enter first name" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lastName"
                                    placeholder="Enter last name" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="userEmail"
                                    placeholder="Enter email address" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="userPassword"
                                    placeholder="Enter password" required>
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="userRole" required>
                                        <option value="User">User</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>

                                <!-- CSRF Token -->
                                <input type="hidden" name="csrfToken" 
                                value="<?php echo htmlspecialchars($_SESSION['csrfToken'], ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="addUserBtn" class="btn btn-primary">Add User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDIT USER MODAL -->
            <div class="modal fade" id="editUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="users.php">
                                <input type="hidden" name="editUserID" id="editUserID">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="firstName" id="editFirstName"
                                    placeholder="Enter first name" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lastName" id="editLastName"
                                    placeholder="Enter last name" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="userEmail" id="editUserEmail"
                                    placeholder="Enter email address" required>
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="userRole" id="editUserRole" required>
                                        <option value="User">User</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>

                                <!-- CSRF Token -->
                                <input type="hidden" name="csrfToken" 
                                value="<?php echo htmlspecialchars($_SESSION['csrfToken'], ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="editUserBtn" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Edit Modal Population Script -->
    <script>
        var editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', function(event) {
            var editBtn       = event.relatedTarget;
            var userID        = editBtn.getAttribute('data-id');
            var userFirstName = editBtn.getAttribute('data-firstname');
            var userLastName  = editBtn.getAttribute('data-lastname');
            var userEmail     = editBtn.getAttribute('data-email');
            var userRole      = editBtn.getAttribute('data-role');

            document.getElementById('editUserID').value        = userID;
            document.getElementById('editFirstName').value     = userFirstName;
            document.getElementById('editLastName').value      = userLastName;
            document.getElementById('editUserEmail').value     = userEmail;
            document.getElementById('editUserRole').value      = userRole;
        });
    </script>

    <?php include '../includes/theme.php'; ?>

</body>
</html>