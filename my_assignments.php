<?php
ob_start();
require('top.inc.php');

// Check if the user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header('location: login.php');
    die();
}

// Fetch the current user's project assignments
$user_id = $_SESSION['USER_ID'];
$assignments_query = "SELECT * FROM project_assignments WHERE employee_id = $user_id";
$assignments_res = mysqli_query($con, $assignments_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments</title>
       <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">

    <!-- <style>
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left: 250px;
            background-color: white;
            padding: 20px;
            padding-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav h1 {
            margin: 0;
            padding: 0;
            color: black;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            width: 80%;
            margin-top: 120px;
            margin-left: 280px;
            padding: 20px;
            padding-left: 20px;
            padding-right: 20px;
            box-sizing: border-box;
            background-color: white;
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

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .action-th {
            text-align: center;
        }

        form {
            display: inline-block;
        }
    </style> -->
</head>

<body>
    <div class="nav">
        <h1>My Assignment</h1>
    </div>

    <div class="content">
        <h2>Your Project Assignments</h2>
        <table>
            <thead>
                   <tr>
                    <th>Project ID</th>
                    <th>Project Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Description</th>
                    <th>Updated Status</th>
                    <th class="action-th">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = mysqli_fetch_assoc($assignments_res)) : ?>
                    <tr>
                        <td><?= $assignment['project_id']; ?></td>
                        <?php
                        $projectDetails = getProjectDetails($con, $assignment['project_id']);
                        ?>
                        <td><?= $projectDetails['name']; ?></td>
                        <td><?= $projectDetails['start_date']; ?></td>
                        <td><?= $projectDetails['end_date']; ?></td>
                        <td><?= $projectDetails['description']; ?></td>
                        <td>
                            <?= htmlspecialchars($projectDetails['status']); ?>
                        </td>
                        <td class="action-th">
                            <form action="" method="post">
                                <input type="hidden" name="projectId" value="<?= $assignment['project_id']; ?>">
                                <select name="status_update">
                                    <option value="Pending" <?= ($projectDetails['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Ongoing" <?= ($projectDetails['status'] == 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="Completed" <?= ($projectDetails['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                </select>
                                <button type="submit" name="update_status">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Process status update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_status'])) {
        $projectIdToUpdate = $_POST['projectId'];
        $newStatus = $_POST['status_update'];

        // Update the project status in the database
        $updateStatusQuery = "UPDATE projects SET status = '$newStatus' WHERE id = $projectIdToUpdate";
        mysqli_query($con, $updateStatusQuery);
        
        // Redirect to prevent resubmission on page refresh
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}

// Function to get project details based on project_id
function getProjectDetails($con, $project_id)
{
    $project_query = "SELECT * FROM projects WHERE id = $project_id";
    $project_res = mysqli_query($con, $project_query);

    if ($project_res && mysqli_num_rows($project_res) > 0) {
        $projectDetails = mysqli_fetch_assoc($project_res);
        return $projectDetails;
    } else {
        return [
            'name' => 'Project Not Found',
            'start_date' => 'N/A',
            'end_date' => 'N/A',
            'description' => 'N/A',
            'status' => 'N/A',
        ];
    }
}
?>