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

<script>
    function toggleEditRow(button) {
      const row = button.closest('tr');
      const titleCell = row.cells[1];
      const emailCell = row.cells[2];
      const roleCell = row.cells[3];

      if (button.textContent === "Edit") {

        const titleValue = titleCell.textContent.trim();
        const emailValue = emailCell.textContent.trim();
        const roleValue = roleCell.textContent.trim().split(', ');

        titleCell.innerHTML = `<input type="text" value="${titleValue}" />`;
        emailCell.innerHTML = `<input type="email" value="${emailValue}" />`;
        roleCell.innerHTML = `
          <select multiple>
            <option value="Admin" ${roleValue.includes("Admin") ? "selected" : ""}>Admin</option>
            <option value="Developer" ${roleValue.includes("Developer") ? "selected" : ""}>Developer</option>
            <option value="Manager" ${roleValue.includes("Manager") ? "selected" : ""}>Manager</option>
            <option value="Designer" ${roleValue.includes("Designer") ? "selected" : ""}>Designer</option>
          </select>
        `;

        button.textContent = "Save";
      } else {
        
        const updatedTitle = titleCell.querySelector('input').value;
        const updatedEmail = emailCell.querySelector('input').value;
        const updatedRoles = Array.from(roleCell.querySelector('select').selectedOptions).map(option => option.value);

        titleCell.textContent = updatedTitle;
        emailCell.textContent = updatedEmail;
        roleCell.textContent = updatedRoles.join(', ');

        button.textContent = "Edit";
      }
    }
  </script>
<?php include('../../examples/includes/footer.php'); ?>