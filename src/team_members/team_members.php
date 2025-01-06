<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

// echo $_SESSION['user_id']
?>

<h1>Team Members</h1>
<button class="add-user">+ Add User</button>
<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Title</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Simulating fetching data from the database
        $team_members = [
            ["name" => "Codewave Asante", "title" => "Administrator", "email" => "admin@gmail.com", "role" => "Admin"],
            ["name" => "John Doe", "title" => "Software Engineer", "email" => "john.doe@example.com", "role" => "Developer"],
            ["name" => "Jane Smith", "title" => "Product Manager", "email" => "jane.smith@example.com", "role" => "Manager"]
        ];

        foreach ($team_members as $index => $member) {
            echo "<tr data-index='$index'>";
            echo "<td>{$member['name']}</td>";
            echo "<td>{$member['title']}</td>";
            echo "<td>{$member['email']}</td>";
            echo "<td>{$member['role']}</td>";
            echo "<td><button class='edit-btn'>Edit</button> <button class='delete-btn'>Delete</button></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</div>

<script>
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const cells = row.querySelectorAll('td');

            const name = cells[0].innerText;
            const title = cells[1].innerText;
            const email = cells[2].innerText;
            const role = cells[3].innerText;

            const nameInput = `<input type='text' value='${name}'>`;
            const titleInput = `<input type='text' value='${title}'>`;
            const emailInput = `<input type='text' value='${email}'>`;
            const roleInput = `<input type='text' value='${role}'>`;

            cells[0].innerHTML = nameInput;
            cells[1].innerHTML = titleInput;
            cells[2].innerHTML = emailInput;
            cells[3].innerHTML = roleInput;

            event.target.innerText = 'Save';
            event.target.classList.add('save-btn');

            event.target.removeEventListener('click', arguments.callee);
            event.target.addEventListener('click', () => {
                const updatedName = cells[0].querySelector('input').value;
                const updatedTitle = cells[1].querySelector('input').value;
                const updatedEmail = cells[2].querySelector('input').value;
                const updatedRole = cells[3].querySelector('input').value;

                cells[0].innerText = updatedName;
                cells[1].innerText = updatedTitle;
                cells[2].innerText = updatedEmail;
                cells[3].innerText = updatedRole;

                event.target.innerText = 'Edit';
                event.target.classList.remove('save-btn');
            });
        });
    });
</script>
<?php include('../../examples/includes/navbar.php'); ?>

<?php include('../../examples/includes/footer.php'); ?>