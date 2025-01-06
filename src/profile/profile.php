<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

// echo $_SESSION['user_id']
?>

<header>
    <div>
        <h1>Welcome, Tasnim</h1>
        <p>17 november,2024</p>
    </div>
    <!-- search bar start  -->
    <div class="search-bar">
        <input type="text" placeholder="Search" class="search-input">
        <button class="search-button">Search</button>
    </div>
    <!-- search bar end  -->
    <!-- notification  start -->
    <div class="notification">
        <i class="fa-solid fa-bell" class="icon"></i>
        <img src="assets/img/user.png" alt="" srcset="" class="nofication-img">
    </div>
    <!-- notification  end -->
</header>


<div class="orange">

</div>



<div class="profile-section">
    <button class="edit-btn">Edit</button>
    <img src="../../assets/img/profile.png" class="profile-img" alt="" srcset="">

    </button>
    <form>
        <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" placeholder="Your First Name">
        </div>
        <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" placeholder="Last Name">
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <select id="country">
                <option value="">Country</option>
                <option value="bangladesh">Bangladesh</option>
                <option value="usa">USA</option>
                <option value="india">India</option>
            </select>
        </div>
    </form>


    <div class="email-section">
        <p>My email address</p>
        <p>tasnim@example.com</p>
        <p>1 month ago</p>
        <button>Add Email Address</button>
    </div>
</div>
</div>



<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>