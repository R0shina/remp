<?php
ob_start();
require('top.inc.php');

// Check if the user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header('location: login.php');
    die();
}

// Assume you have a role stored in the session (employee or admin)
$userRole = $_SESSION['USER_ROLE'];

// Check if the project ID is provided in the URL
if (!isset($_GET['projectId'])) {
    // Redirect to the previous page or handle the situation as needed
    header('location: your_previous_page.php');
    die();
}

$project_id = $_GET['projectId'];

// Fetch the project details
$projectDetails = getProjectDetails($con, $project_id);

// Fetch status updates based on the user role
if ($userRole === '1') {
    $statusQuery = "SELECT * FROM project_assignments WHERE project_id = $project_id";
} else {
    $userId = $_SESSION['USER_ID'];
    $statusQuery = "SELECT * FROM project_assignments WHERE project_id = $project_id AND employee_id = $userId";
}

$statusRes = mysqli_query($con, $statusQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Updates</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        .content {
            width: 80%;
            margin-top: 120px;
            margin-left: 10%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="content">
        <h2>Project Status Updates</h2>
        <p>Project Name: <?= $projectDetails['name']; ?></p>
        <p>Start Date: <?= $projectDetails['start_date']; ?></p>
        <p>End Date: <?= $projectDetails['end_date']; ?></p>

        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Status</th>
                    <th>Status Update</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($status = mysqli_fetch_assoc($statusRes)) : ?>
                    <tr>
                        <td><?= $status['employee_name']; ?></td>
                        <td><?= $status['status']; ?></td>
                        <td><?= $status['status_update']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Additional PHP code for processing or updating the status can be added here
?>
