<?php
ob_start();
require('top.inc.php');

// Check if the user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header('location: login.php');
    die();
}

// Check if the project ID is provided in the URL

$project_id = $_GET['projectId'];

// Fetch the project details
$project_query = "SELECT * FROM projects WHERE id = $project_id";
$project_res = mysqli_query($con, $project_query);

if ($project_res && mysqli_num_rows($project_res) > 0) {
    $projectDetails = mysqli_fetch_assoc($project_res);
} else {
    $projectDetails = [
        'id' => 'N/A',
        'name' => 'Project Not Found',
        'start_date' => 'N/A',
        'end_date' => 'N/A',
        'description' => 'N/A',
    ];
}

// Additional PHP code for processing the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission and update the project status
    $newStatus = $_POST['status_update'];

    // Add your code to update the project status in the database or perform any other necessary actions
    // For example, you might use mysqli_query to update the status in the 'projects' table

    // Display the updated status
    echo '<div class="content">';
    echo '<h2>Updated Project Status</h2>';
    echo '<p>Project ID: ' . $project_id . '</p>';
    echo '<p>New Status: ' . htmlspecialchars($newStatus) . '</p>';
    echo '</div>';
    exit; // Stop further execution to prevent displaying the original form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project Status</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
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
            margin-top: 80px;
            margin-left: 280px;
            padding: 20px;
            padding-left: 20px;
            padding-right: 20px;
            box-sizing: border-box;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
        }

        button {
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        textarea, input, select {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="nav">
        <h1>Update Project Status</h1>
    </div>

    <div class="content">
        <h2>Project Details</h2>

        <p>Project ID: <?= $projectDetails['id']; ?></p>
        <p>Project Name: <?= $projectDetails['name']; ?></p>
        <p>Start Date: <?= $projectDetails['start_date']; ?></p>
        <p>End Date: <?= $projectDetails['end_date']; ?></p>

        <h2>Update Project Status</h2>

        <form action="process_status_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="project_id" value="<?= $project_id; ?>">

            <label for="status">Project Status:</label>
            <textarea name="status" id="status" required></textarea>

            <label for="file">Upload File:</label>
            <input type="file" name="file" id="file">

            <label for="status_update">Update Status:</label>
            <select name="status_update" id="status_update" required>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <!-- Add more options as needed -->
            </select>

            <button type="submit">Submit Update</button>
        </form>
    </div>
</body>

</html>

<?php
// Additional PHP code for processing the form submission can be added here
?>
