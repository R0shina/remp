<?php
ob_start();

require('top.inc.php');

// Function to get leave types with total leave days
function getLeaveTypes()
{
    global $con;
    $leaveTypes = array();
    $sql = "SELECT id, leave_type, max_days_per_year FROM leave_type";
    $res = mysqli_query($con, $sql);

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $leaveTypes[$row['id']] = array(
                'leave_type' => $row['leave_type'],
                'max_days_per_year' => $row['max_days_per_year']
            );
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }

    return $leaveTypes;
}


function getTotalLeaveCountOfYear($leave_type_id, $user_id)
{
    global $con;
    $count = 0;
    $sql = "SELECT sum(leave_to-leave_from+1) as count FROM `leave` WHERE leave_id = $leave_type_id AND employee_id = $user_id and leave_status=2";
    $res = mysqli_query($con, $sql);

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (isset($row['count'])) {
                $count = $row['count'];
            }
        }
    }

    return $count;
}

$leaveTypes = getLeaveTypes();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">
    <style>
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            border: 5px solid #e0e0e0;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .table td:last-child {
            border-right: none;
        }

        .table td a {
            color: #007bff;
            text-decoration: none;
        }

        .table td a:hover {
            text-decoration: underline;
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

        /* .content {
            margin-top: 100px;
            margin-left: 280px;
            padding: 10px;
            box-sizing: border-box;
        } */

        .card {
            /* background-color: #f2f2f2; */
            border-radius: 5px;
            /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
            padding: 20px;
            width: 98%;
        }
    </style>
</head>

<body>
    <div class="nav">
        <h1>My Leave</h1>
    </div>
    <div class="content ">
        <div class="orders">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($_SESSION['ROLE'] == 2) { ?>
                                <h2 class="box_title_link">
                                    <a href="add_leave.php" width="15%">Add Leave</a> |
                                    <a href="my leave.php">My Leave</a>
                                </h2>
                            <?php } ?>
                        </div>
                        <h2 class="box-title">Leave List</h2>
                        <div class="card-body--">
                            <div class="table-stats order-table ov-h">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Leave Type</th>
                                            <th>Total Leave Days</th>
                                            <th>Remaining Leave</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($leaveTypes as $leaveTypeId => $leaveTypeData) { ?>
                                            <tr>
                                                <td><?php echo $leaveTypeData['leave_type']; ?></td>
                                                <td><?php echo $leaveTypeData['max_days_per_year']; ?></td>
                                                <td>
                                                    <?php
                                                    $user_id = $_SESSION["USER_ID"];
                                                    $totalLeaveDays = $leaveTypeData['max_days_per_year'];
                                                    $takenLeaveDays = getTotalLeaveCountOfYear($leaveTypeId, $user_id);
                                                    $remainingLeaveDays = $totalLeaveDays - $takenLeaveDays;
                                                    echo $remainingLeaveDays;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
ob_end_flush();
require('footer.inc.php');
?>
