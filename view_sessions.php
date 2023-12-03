<?php
ob_start();
require('top.inc.php');

// Check if a specific date range is provided
$start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

// Fetch sessions based on the date range
$session_query = mysqli_query($con, "SELECT s.id, s.user_id, e.name as employee_name, s.login_time, s.logout_time 
                                     FROM `session` s 
                                     JOIN `employee` e ON s.user_id = e.id
                                     WHERE s.login_time >= '$start_date' AND s.logout_time <= '$end_date'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee Sessions</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        table {
            width: 1000px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left: 250px;
            background-color: white;
            padding: 20px;
            padding-top: 20px;
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
    </style>
</head>
<body>
    <div class="nav">
        <h1>View Employee Sessions</h1>
    </div>

    <form action="" method="get">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">

        <button type="submit">Filter Sessions</button>
    </form>

    <table>
        <tr>
            <th>Session ID</th>
            <th>User ID</th>
            <th>Employee Name</th>
            <th>Login Time</th>
            <th>Logout Time</th>
        </tr>
        <?php while ($session_row = mysqli_fetch_assoc($session_query)) { ?>
            <tr>
                <td><?php echo $session_row['id']; ?></td>
                <td><?php echo $session_row['user_id']; ?></td>
                <td><?php echo $session_row['employee_name']; ?></td>
                <td><?php echo $session_row['login_time']; ?></td>
                <td><?php echo $session_row['logout_time']; ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>

<?php
ob_end_flush(); 
require('footer.inc.php');
?>
