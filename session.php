<?php
ob_start();
require('top.inc.php');
echo '<pre>';
print_r($_GET);
echo '</pre>';

$session_query = mysqli_query($con, "SELECT s.id, s.user_id, e.name as employee_name, s.login_time, s.logout_time 
                                      FROM `session` s 
                                      JOIN `employee` e ON s.user_id = e.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Session Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        /* body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        } */

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

    .header {
    display: flex;
    align-items: center;
    justify-content: space-between; 
    width: 100%; 
    margin-bottom: 10px;
}

.header h2 {
    margin: 0;
}

.header a {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.header a:hover {
    background-color: #0056b3;
}


    </style>
</head>
<body>
    <div class="nav">
        <h1>Session</h1>
    </div>

    <table>
    <!-- <div class="header">
        <h2>Monthly Salary Calculation</h2>
        <a href="view_sessions.php">View Details</a>
    </div> -->

    <tr>
            <td colspan="6" class="header">
                <h2>View Session</h2>
                <a href="view_sessions.php">View Details</a>
            </td>
    </tr>

        <tr>
            <!-- <th>Session ID</th> -->
          <a href="view_sessions.php" class="button">View Old Sessions</a>
            <th>User ID</th>
            <th>Employee Name</th>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Session Duration</th>
        </tr>
        <?php while ($session_row = mysqli_fetch_assoc($session_query)) { ?>
            <tr>
                <td><?php echo $session_row['user_id']; ?></td>
                <td><?php echo $session_row['employee_name']; ?></td>
                <td><?php echo $session_row['login_time']; ?></td>
                <td><?php echo $session_row['logout_time']; ?></td>
                <td><?php echo calculateSessionDuration($session_row['login_time'], $session_row['logout_time']); ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>

<?php
ob_end_flush(); 
require('footer.inc.php');
?>

<?php
function calculateSessionDuration($loginTime, $logoutTime) {
    // Assuming the format of login_time and logout_time is 'Y-m-d H:i:s'
    // $loginTimestamp = strtotime($loginTime);
    $loginTimestamp = DateTime::createFromFormat('Y-m-d H:i:s', $loginTime)->getTimestamp();

    $logoutTimestamp = strtotime($logoutTime);

    $duration = $logoutTimestamp - $loginTimestamp;

    $formattedDuration = gmdate("H:i:s", $duration);

    return $formattedDuration;
}
?>
