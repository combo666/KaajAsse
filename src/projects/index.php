<?php
session_start();
include '../../conf/database/db_connect.php';
function generateUniqueCode($existingCodes, $length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);

    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $charactersLength - 1)];
        }
    } while (in_array($code, $existingCodes));

    return $code;
}

$user_id = mysqli_real_escape_string($connect, $_SESSION['user_id']);

$query = "SELECT p.name, p.owner
FROM KaajAsse.projects p
INNER JOIN KaajAsse.project_user pu ON p.id = pu.project_id
WHERE pu.user_id = $user_id";

$result = mysqli_query($connect, $query);


?>


<?php
if (isset($_POST["create"])) {
    // Get user information and sanitize input
    $name = mysqli_real_escape_string($connect, $_POST["pname"]);
    $uname = mysqli_real_escape_string($connect, $_SESSION['uname']);
    $uid = $_SESSION['user_id'];

    // Fetch existing invitation codes
    $query4 = "SELECT projects.invitation_code AS code FROM projects";
    $result4 = mysqli_query($connect, $query4);
    $existingCodes = [];
    while ($row = mysqli_fetch_assoc($result4)) {
        $existingCodes[] = $row['code'];
    }

    // Generate a unique invitation code
    $newInvitationCode = generateUniqueCode($existingCodes);

    // Insert the new project
    $query1 = "INSERT INTO KaajAsse.projects(name, user_id, owner, invitation_code) 
               VALUES('$name', '$uid', '$uname', '$newInvitationCode')";

    $result1 = mysqli_query($connect, $query1);

    if ($result1) {
        // Insert project-user relationship
        $newProjectId = mysqli_insert_id($connect);
        $query2 = "INSERT INTO KaajAsse.project_user(project_id, user_id) VALUES('$newProjectId', '$uid')";
        $result2 = mysqli_query($connect, $query2);

        if ($result2) {
            echo json_encode([
                "status" => "success",
                "project" => [
                    "name" => $name,
                    "owner" => $uname,
                ]
            ]);
            exit;
        }
    }
    // Return error if anything fails
    echo json_encode(["status" => "error", "message" => "Error creating project: " . mysqli_error($connect)]);
    exit;
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
    <link rel="stylesheet" href="../../assets/css/projects.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="main-section">
        <h1 style="text-align:center; padding-top: 50px;">Welcome, <?php echo $_SESSION['uname'] . "!"; ?></h1>
        <div class="join-invitation">
            <form action="" method="get">
                <input type="text" placeholder="Project Invitation Code">
                <button style="cursor: pointer">JOIN</button><br>
            </form>
        </div>
        <div class="project-lists">
            <h2>My Projects</h2>
            <br><br>
            <button id="createProjectBtn" style="margin: 10px 0; float:right; padding: 10px; cursor: pointer">Create a
                new project</button>

            <div id="projectContainer">

                <?php

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="project-item">
                            <div class="project-title"><?php echo $row['name']; ?></div>
                            <div class="project-owner"><?php echo $row['owner']; ?></div>
                            <button class="btn-project" style="cursor: pointer;">Go</button>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div id="createProjectModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create a New Project</h2>
            <form id="createProjectForm">
                <input type="text" id="projectName" placeholder="Project Name" name="pname" required>
                <button type="submit" style="cursor: pointer; margin-top: 10px;" name="create">Create</button>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById('createProjectModal');
    const btn = document.getElementById('createProjectBtn');
    const span = document.getElementsByClassName('close')[0];
    const projectContainer = document.getElementById('projectContainer');

    // Show the modal when the "Create a new project" button is clicked
    btn.onclick = function () {
        modal.style.display = 'block';
    };

    // Close the modal when the close button is clicked
    span.onclick = function () {
        modal.style.display = 'none';
    };

    // Close the modal if clicked outside of it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };

    // Handle form submission
    document.getElementById('createProjectForm').onsubmit = async function (e) {
        e.preventDefault(); // Prevent page refresh

        // Create FormData from the form
        const formData = new FormData(this);

        // Send the form data using fetch
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });

        // Parse the JSON response
        const result = await response.json();

        if (result.status === 'success') {
            // Create a new project item and add it to the project container
            const newProject = document.createElement('div');
            newProject.classList.add('project-item');
            newProject.innerHTML = `
                <div class="project-title">${result.project.name}</div>
                <div class="project-owner">${result.project.owner}</div>
                <button class="btn-project" style="cursor: pointer;">Go</button>
            `;
            projectContainer.appendChild(newProject);

            // Close the modal and reset the form
            modal.style.display = 'none';
            document.getElementById('projectName').value = '';
        } else {
            // Show an alert if there is an error
            alert(result.message);
        }
    };
</script>

</body>

</html>