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
    $uname = trim($_POST['uname']);

    if (!empty($first_name) && !empty($last_name) && !empty($uname)) {
        $update_query = "UPDATE KaajAsse.user SET first_name = ?, last_name = ?, uname = ? WHERE LOWER(user_email) = LOWER(?)";
        $update_stmt = $connect->prepare($update_query);

        if (!$update_stmt) {
            $error_message = "Query preparation failed: " . $connect->error;
        } else {
            $update_stmt->bind_param("ssss", $first_name, $last_name, $uname, $user_email);

            if ($update_stmt->execute()) {
                $success_message = "Profile updated successfully!";
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['uname'] = $uname;
            } else {
                $error_message = "Update failed: " . $update_stmt->error;
            }
        }
    } else {
        $error_message = "All fields must be filled out.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <div>
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?></h1>
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
               readonly required>
    </div>
    <div class="form-group">
        <label for="last-name">Last Name</label>
        <input type="text" id="last-name" name="last_name" 
               value="<?php echo htmlspecialchars($user['last_name']); ?>" 
               readonly required>
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

<script>
function toggleEdit() {
    const form = document.getElementById('profileForm');
    const firstNameInput = document.getElementById('first-name');
    const lastNameInput = document.getElementById('last-name');
    const formActions = form.querySelector('.form-actions');
    
    firstNameInput.readOnly = !firstNameInput.readOnly;
    lastNameInput.readOnly = !lastNameInput.readOnly;
    
    formActions.style.display = firstNameInput.readOnly ? 'none' : 'flex';
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

    <div class="email-section">
        <p>My email address</p>
        <p><?php echo htmlspecialchars($user['user_email']); ?></p>
        <button>Add Email Address</button>
    </div>
</div>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>

</body>
</html>
