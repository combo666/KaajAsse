
<?php
include '../../conf/database/db_connect.php';
    if(isset($_POST['sign-in'])){
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $password = mysqli_real_escape_string($connect, $_POST['password']);

        $select = " SELECT * FROM KaajAsse.user WHERE user_email = '$email';";
        $result = mysqli_query($connect, $select);
        // echo $result;
        if(mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result); 
            if ($password == $user['user_pass']) {
                // session_start();
                // $_SESSION['user_id'] = $user['user_id']; 
                // $_SESSION['user_email'] = $user['user_email'];
                
                // Redirect to a welcome or dashboard page
                // header("Location: welcome.php");
                echo "good to see ya";
                // exit();


            } else {
                echo "Wrong password!";
            }

        } else {
            echo "User not found!";
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
            <div class="bandage bandage-top-left"></div>
            <div class="bandage bandage-top-right"></div>

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
                    <input type="text" name="fullname" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="terms" required>
                        I accept the <a href="#">Terms and conditions</a>
                    </label>
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