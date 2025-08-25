<?php
include '../../conf/database/db_connect.php';

// Use prepared statements and PHP password hashing for secure auth
if (isset($_POST['sign-in'])) {
    $email = trim($_POST['email']);
    $passwordInput = $_POST['password'];

    // Prepared statement to fetch user by email
    $stmt = mysqli_prepare($connect, "SELECT user_id, user_email, user_pass, user_role, first_name, last_name FROM KaajAsse.user WHERE user_email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $storedHash = $user['user_pass'];

        // Modern verification
        if (password_verify($passwordInput, $storedHash)) {
            session_start();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['uname'] = $user['first_name'] . " " . $user['last_name'];

            header("Location: ../dashboard/dashboard.php");
            exit();

        } else {
            // Legacy fallback for old md5-hashed passwords: re-hash on successful match
            if (strlen($storedHash) === 32 && md5($passwordInput) === $storedHash) {
                // Re-hash with password_hash
                $newHash = password_hash($passwordInput, PASSWORD_DEFAULT);
                $updateStmt = mysqli_prepare($connect, "UPDATE KaajAsse.user SET user_pass = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($updateStmt, 'si', $newHash, $user['user_id']);
                mysqli_stmt_execute($updateStmt);

                session_start();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['uname'] = $user['first_name'] . " " . $user['last_name'];

                header("Location: ../dashboard/dashboard.php");
                exit();
            }

            echo "Wrong password!";
        }

    } else {
        echo "User not found!";
    }

    mysqli_stmt_close($stmt);

} else if (isset($_POST["sign-up"])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    $errors = [];

    if ($password !== $cpassword) {
        $errors[] = 'Passwords did not match!';
    }

    // Check if email already exists
    $checkStmt = mysqli_prepare($connect, "SELECT user_id FROM KaajAsse.user WHERE user_email = ?");
    mysqli_stmt_bind_param($checkStmt, 's', $email);
    mysqli_stmt_execute($checkStmt);
    $checkRes = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkRes) > 0) {
        $errors[] = 'User already exists!';
    }

    mysqli_stmt_close($checkStmt);

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = mysqli_prepare($connect, "INSERT INTO KaajAsse.user (first_name, last_name, user_email, user_pass) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertStmt, 'ssss', $fname, $lname, $email, $passwordHash);
        $ok = mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);

        if ($ok) {
            echo "Now Login";
        } else {
            echo "Registration failed.";
        }
    } else {
        foreach ($errors as $err) echo htmlspecialchars($err) . "<br>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaajAsse</title>
    <link rel="icon" href="../../assets/img/icon.ico">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/task_calendar.css">
    <link rel="stylesheet" href="../../assets/css/signin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="form-section">
        <div class="signin-box">
            <!-- <div class="bandage bandage-top-left"></div>
            <div class="bandage bandage-top-right"></div> -->

            <div class="toggle-container">
                <div id="btn"></div>
                <button type="button" class="toggle-btn" onclick="switchToSignIn()">Sign In</button>
                <button type="button" class="toggle-btn" onclick="switchToSignUp()">Sign Up</button>
            </div>

            <form id="signin-form" method="POST">
                <div class="form-group">
                    <input type="text" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="signin-btn" name="sign-in">SIGN IN</button>
                </div>
            </form>

            <form id="signup-form" method="POST" style="display: none;">
                <div class="form-group">
                    <input type="text" name="fname" placeholder="First Name" required>
                </div>
                <div class="form-group">
                <input type="text" name="lname" placeholder="Last Name">
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="cpassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="signin-btn" name="sign-up">SIGN UP</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const btn = document.getElementById('btn');
        const signinForm = document.getElementById('signin-form');
        const signupForm = document.getElementById('signup-form');

        function switchToSignIn() {
            btn.style.left = '0';
            signinForm.style.display = 'block';
            signupForm.style.display = 'none';
        }

        function switchToSignUp() {
            btn.style.left = '50%';
            signinForm.style.display = 'none';
            signupForm.style.display = 'block';
        }
    </script>

    <?php include '../../examples/includes/footer.php' ?>