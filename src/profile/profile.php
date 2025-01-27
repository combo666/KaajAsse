<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_email'])) {
    header('Location: ../login/signin.php');
    exit();
}

// Fetch user data from session
$user_email = $_SESSION['user_email'];
$query = "SELECT * FROM KaajAsse.user WHERE LOWER(user_email) = LOWER(?)";
$stmt = $connect->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $connect->error);
}

$stmt->bind_param("s", $user_email);
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("No user found with email: " . htmlspecialchars($user_email));
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Only update the fields provided by the user
    $update_query = "UPDATE KaajAsse.user SET";
    $params = [];
    $types = "";

    if (!empty($first_name)) {
        $update_query .= " first_name = ?,";
        $params[] = $first_name;
        $types .= "s";
    }

    if (!empty($last_name)) {
        $update_query .= " last_name = ?,";
        $params[] = $last_name;
        $types .= "s";
    }

    // Remove the trailing comma and add the WHERE clause
    $update_query = rtrim($update_query, ',') . " WHERE LOWER(user_email) = LOWER(?)";
    $params[] = $user_email;
    $types .= "s";

    $update_stmt = $connect->prepare($update_query);

    if (!$update_stmt) {
        $error_message = "Query preparation failed: " . $connect->error;
    } else {
        $update_stmt->bind_param($types, ...$params);

        if ($update_stmt->execute()) {
            if (!empty($first_name)) $user['first_name'] = $first_name;
            if (!empty($last_name)) $user['last_name'] = $last_name;
        } else {
        }
    }
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../index.php");
    exit();
}
?>

<div>
    <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' '. $user['last_name']); ?></h1>
</div>

<div class="orange"></div>

<div class="profile-section">
    <?php if (isset($success_message)): ?>
        <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <button class="edit-btn" onclick="toggleEdit()">Edit</button>
    <img src="../../assets/img/profile.png" class="profile-img" alt="">

    <form method="POST" id="profileForm">
        <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="first_name"
                value="<?php echo htmlspecialchars($user['first_name']); ?>"
                readonly>
        </div>
        <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last_name"
                value="<?php echo htmlspecialchars($user['last_name']); ?>"
                readonly>
        </div>
        <div class="form-group">
            <label for="user_role">User Role</label>
            <input type="text" id="user_role" name="user_role"
                value="<?php echo htmlspecialchars($user['user_role']); ?>"
                class="form-input" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email"
                value="<?php echo htmlspecialchars($user['user_email']); ?>"
                class="form-input" readonly>
        </div>
        <div class="form-actions" style="display: none;">
            <button type="button" onclick="cancelEdit()" class="btn cancel-btn">Cancel</button>
            <button type="submit" name="update_profile" class="btn update-btn">Update Profile</button>
        </div>
    </form>

    <form method="POST" class="logout-form">
        <button type="submit" name="logout" class="logout-btn">Log Out</button>
    </form>
</div>

<script>
    function toggleEdit() {
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input:not([type="email"]):not([name="user_role"])');
        const formActions = form.querySelector('.form-actions');

        inputs.forEach(input => {
            input.readOnly = !input.readOnly;
        });

        formActions.style.display = inputs[0].readOnly ? 'none' : 'flex';
    }

    function cancelEdit() {
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input:not([type="email"]):not([name="user_role"])');
        const formActions = form.querySelector('.form-actions');

        inputs.forEach(input => {
            input.readOnly = true;
            input.value = input.defaultValue;
        });

        formActions.style.display = 'none';
    }
</script>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>