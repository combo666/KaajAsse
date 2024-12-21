<?php
session_start();
include '../../conf/database/db_connect.php';

function generateInvitationCode($existingCodes, $length = 6)
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // if (isset($_POST['action']) && $_POST['action'] === 'select_project') {
    //     // Handle project selection
    //     if (isset($_POST['project_name'], $_POST['project_owner'])) {
    //         $_SESSION['project_name'] = $_POST['project_name'];
    //         $_SESSION['project_owner'] = $_POST['project_owner'];
    //         $_SESSION['project_id'] = $_POST['project_id'];
    //         echo json_encode(["status" => "success", "message" => "Project saved to session."]);
    //         header("Location: ../dashboard/dashboard.php");
    //         exit();
    //     } else {
    //         echo json_encode(["status" => "error", "message" => "Required project details are missing."]);
    //     }
    //     exit;
    // }


    $response = [];

    if (isset($_SESSION['user_id'], $_SESSION['uname'], $_POST['pname'])) {
        $name = mysqli_real_escape_string($connect, $_POST['pname']);
        $uname = mysqli_real_escape_string($connect, $_SESSION['uname']);
        $uid = $_SESSION['user_id'];

        $query4 = "SELECT invitation_code AS code FROM KaajAsse.projects";
        $result4 = mysqli_query($connect, $query4);
        $existingCodes = [];
        while ($row = mysqli_fetch_assoc($result4)) {
            $existingCodes[] = $row['code'];
        }

        $newInvitationCode = generateInvitationCode($existingCodes);

        $query1 = "INSERT INTO KaajAsse.projects(name, user_id, owner, invitation_code) 
                   VALUES('$name', '$uid', '$uname', '$newInvitationCode')";
        if (mysqli_query($connect, $query1)) {
            $newProjectId = mysqli_insert_id($connect);

            $query2 = "INSERT INTO KaajAsse.project_user(project_id, user_id) VALUES('$newProjectId', '$uid')";
            if (mysqli_query($connect, $query2)) {
                $response = [
                    "status" => "success",
                    "project" => [
                        "name" => $name,
                        "owner" => $uname,
                        "id" => $newProjectId
                    ]
                ];
            } else {
                $response = ["status" => "error", "message" => "Error linking user to the project."];
            }
        } else {
            $response = ["status" => "error", "message" => "Error creating project: " . mysqli_error($connect)];
        }
    } else {
        $response = ["status" => "error", "message" => "Required fields are missing."];
    }

    echo json_encode($response);
    exit;
}

if (isset($_SESSION['user_id'], $_SESSION['uname'])) {
$user_id = mysqli_real_escape_string($connect, $_SESSION['user_id']);
$query = "SELECT p.name, p.owner, p.id
          FROM KaajAsse.projects p
          INNER JOIN KaajAsse.project_user pu ON p.id = pu.project_id
          WHERE pu.user_id = $user_id";
$result = mysqli_query($connect, $query);
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
                        echo "". $row[""];
                        ?>
                        <form class="project-item" method="get" action="../dashboard/dashboard.php">
                            <input class="project-title" name="project_name" value="<?php echo $row['name']; ?>" readonly>
                            <input class="project-owner" name="project_owner" value="<?php echo $row['owner']; ?>" readonly>
                            <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="action">
                            <button class="btn-project" style="cursor: pointer;" name="go-to-project">Go</button>
                        </form>
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
                <button type="submit" style="cursor: pointer; margin-top: 10px;">Create</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('createProjectModal');
        const btn = document.getElementById('createProjectBtn');
        const span = document.getElementsByClassName('close')[0];
        const projectContainer = document.getElementById('projectContainer');

        btn.onclick = function () {
            modal.style.display = 'block';
        };

        span.onclick = function () {
            modal.style.display = 'none';
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        document.getElementById('createProjectForm').onsubmit = async function (e) {
            e.preventDefault(); // Prevent page refresh

            const formData = new FormData(this);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    const newProject = document.createElement('form');
                    newProject.classList.add('project-item');
                    newProject.setAttribute('method', 'get');
                    newProject.setAttribute('action', '../dashboard/dashboard.php');

                    newProject.innerHTML = `
                        <input class="project-title" name="project_name" value="${result.project.name}" disabled>
                        <input class="project-owner" name="project_owner" value="${result.project.owner}" disabled>
                        <input type="hidden" name="project_id" value="${result.project.id}">
                        <input type="hidden" name="action" value="action">
                        <button class="btn-project" style="cursor: pointer;" name="go-to-project">Go</button>
                    `;

                    projectContainer.appendChild(newProject);


                    modal.style.display = 'none';
                    document.getElementById('projectName').value = '';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('There was an issue creating the project.');
            }
        };
    </script>
</body>

</html>