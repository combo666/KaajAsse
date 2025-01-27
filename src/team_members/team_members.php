<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

$user_role = $_SESSION['user_role'] ?? '';  // Assuming role is stored in session

// Handle adding user from modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $user_id = $_POST['user_id'];
    $assign_role = 'u';
    $query = "UPDATE KaajAsse.user SET user_role = '$assign_role' WHERE user_id = '$user_id' AND user_role IS NULL";
    mysqli_query($connect, $query);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    // $title = $_POST['title'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $query = "UPDATE KaajAsse.user SET last_name = '$name', user_email = '$email', user_role = '$role' WHERE user_id = '$user_id'";
    mysqli_query($connect, $query);
    exit('User updated successfully');
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $query = "DELETE FROM KaajAsse.user WHERE user_id = '$user_id'";
    mysqli_query($connect, $query);
    exit('User deleted successfully');
}

// Fetch users with roles 'a' or 'u'
$team_members_query = "SELECT * FROM KaajAsse.user WHERE user_role IN ('a', 'u')";
$result = mysqli_query($connect, $team_members_query);
$team_members = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch users with null roles for the modal
$null_role_query = "SELECT * FROM KaajAsse.user WHERE user_role IS NULL or user_role = ''";
$null_role_result = mysqli_query($connect, $null_role_query);
$null_users = mysqli_fetch_all($null_role_result, MYSQLI_ASSOC);
?>

<h1>Team Members</h1>
<?php if ($user_role === 'a' || $user_role === 's'): ?>
<button class="add-user" onclick="openModal()">+ Add User</button>
<?php endif; ?>
<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <!-- <th>Title</th> -->
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($team_members as $member): ?>
            <tr data-id="<?php echo $member['user_id']; ?>">
                <td><?php echo $member['last_name']; ?></td>
                <!-- <td><?php echo $member['title']; ?></td> -->
                <td><?php echo $member['user_email']; ?></td>
                <td><?php if($member['user_role'] == 'a') echo "Admin"; else if($member['user_role'] == 's') echo "Super Admin"; else echo "User"?></td>
                <?php if ($user_role === 'a' || $user_role === 's'): ?>
                <td>
                    <button class='edit-btn'>Edit</button>
                    <button class='delete-btn'>Delete</button>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal for adding user -->
<div id="userModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Select User to Add</h2>
        <form method="POST">
            <select name="user_id" required>
                <?php foreach ($null_users as $user): ?>
                    <option value="<?php echo $user['user_id']; ?>">
                        <?php echo $user['last_name'] . ' - ' . $user['user_email']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>
</div>

<script>
const editButtons = document.querySelectorAll('.edit-btn');
const deleteButtons = document.querySelectorAll('.delete-btn');

// Edit button functionality
editButtons.forEach(button => {
    button.addEventListener('click', (event) => {
        const row = event.target.closest('tr');
        const cells = row.querySelectorAll('td');
        const userId = row.dataset.id;

        const name = cells[0].innerText;
        const email = cells[1].innerText;
        const role = cells[2].innerText;

        // Prevent admin from editing other admins
        if ('<?php echo $user_role; ?>' === 'a' && role === 'a') {
            alert('You do not have permission to edit this user.');
            return;
        }

        const nameInput = `<input type='text' name='name' value='${name}'>`;
        const emailInput = `<input type='text' name='email' value='${email}'>`;
        
        // Dropdown for role selection
        const roleSelect = `
            <select name='role'>
                <option value='a' ${role === 'a' ? 'selected' : ''}>Admin</option>
                <option value='u' ${role === 'u' ? 'selected' : ''}>User</option>
            </select>
        `;

        // Replace cells with inputs
        cells[0].innerHTML = nameInput;
        cells[1].innerHTML = emailInput;
        cells[2].innerHTML = roleSelect;

        // Replace action buttons with save and cancel buttons
        const saveButton = `<button onclick='saveEdit(${userId})' class='edit-btn'>Save</button>`;
        const cancelButton = `<button onclick='window.location.reload()' class='delete-btn'>Cancel</button>`;
        cells[3].innerHTML = saveButton + cancelButton;
    });
});

// Delete button functionality
deleteButtons.forEach(button => {
    button.addEventListener('click', (event) => {
        const row = event.target.closest('tr');
        const userId = row.dataset.id;
        const role = row.querySelectorAll('td')[2].innerText;

        // Prevent admin from deleting other admins
        if ('<?php echo $user_role; ?>' === 'a' && role === 'a') {
            alert('You do not have permission to delete this user.');
            return;
        }

        if (confirm('Are you sure you want to delete this user?')) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `delete_user=1&user_id=${userId}`
            })
            .then(() => window.location.reload());
        }
    });
});

// Save edit function
function saveEdit(userId) {
    const row = document.querySelector(`tr[data-id='${userId}']`);
    const inputs = row.querySelectorAll('input');
    const select = row.querySelector('select');

    const formData = new FormData();
    formData.append('edit_user', 1);
    formData.append('user_id', userId);
    formData.append('name', inputs[0].value);  // Name input
    formData.append('email', inputs[1].value); // Email input
    formData.append('role', select.value);     // Role select

    fetch('', { method: 'POST', body: formData })
        .then(() => window.location.reload());
}

// Modal controls
function openModal() {
    document.getElementById('userModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}

</script>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>