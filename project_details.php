<?php
require('top.inc.php');


function getEmployeeName($con, $employeeId)
{
    $sql = "SELECT name FROM projects WHERE id = $employeeId";
    $employee_res = mysqli_query($con, $sql);

    if ($employee_res) {
        if (mysqli_num_rows($employee_res) > 0) {
            $employee = mysqli_fetch_assoc($employee_res);
            return $employee['name'] ?? 'N/A';
        } else {
            return 'Employee not found';
        }
    } else {
        echo "Error: " . mysqli_error($con) . "<br>";
        echo "Query: " . $sql;
        return 'N/A';
    }
}

$projects_res = mysqli_query($con, "SELECT projects.*, employee.name AS assigned_employee_name 
                                    FROM projects 
                                    LEFT JOIN employee ON projects.assigned_employee_id = employee.id");

if ($projects_res) {
    $projects = mysqli_fetch_all($projects_res, MYSQLI_ASSOC);
} else {
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <header>
        <h1>Project List</h1>
    </header>

    <div class="container">
        <!-- Display the list of projects in a table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Assigned Employee</th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
        <?php foreach ($projects as $project) : ?>
            <tr>
                <td><?= $project['id']; ?></td>
                <td><?= $project['name']; ?></td>
                <td><?= $project['description']; ?></td>
                <td><?= $project['start_date']; ?></td>
                <td><?= $project['end_date']; ?></td>
                <td><?= $project['status']; ?></td>
                <!-- Call the function to get the assigned employee name -->
<td><?= getEmployeeName($con, $project['assigned_employee_name']) ?? 'N/A'; ?></td>
                <!-- Add more cells for additional columns -->
            </tr>
        <?php endforeach; ?>
    </tbody>
        </table>
    </div>

</body>

</html>
