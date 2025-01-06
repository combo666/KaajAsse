<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

// echo $_SESSION['user_id']
?>

<div class="header">
    <img src="assets/img/list.png" alt="" class="img-list">
    <h1>Team Members</h1>
</div>
<hr>
<button class="button">Add User</button>

<div>
    <!-- table start -->
    <form action="">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Title</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="avatar">CA</span> Codewave Asante</td>
                    <td>Administrator</td>
                    <td>admin@gmail.com</td>
                    <td>Admin, Manager</td>
                    <td><span class="status active">Active</span></td>
                    <td class="actions">
                        <button type="button" class="edit" onclick="toggleEditRow(this)">Edit</button>
                        <button type="button" class="delete">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><span class="avatar">JD</span> John Doe</td>
                    <td>Software Engineer</td>
                    <td>john.doe@example.com</td>
                    <td>Developer</td>
                    <td><span class="status disabled">Disabled</span></td>
                    <td class="actions">
                        <button type="button" class="edit" onclick="toggleEditRow(this)">Edit</button>
                        <button type="button" class="delete">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>