<?php
ob_start();

require('top.inc.php');
include "./file.php";

// Function to calculate total working days in a month
function getTotalWorkingDaysInMonth($firstDayOfMonth, $lastDayOfMonth, $publicHolidays) {
    $totalWorkingDays = 0;
    $currentDate = strtotime($firstDayOfMonth);

    while ($currentDate <= strtotime($lastDayOfMonth)) {
        $currentDateString = date('Y-m-d', $currentDate);

        if (date('N', $currentDate) != 6 && !in_array($currentDateString, $publicHolidays)) {
            // Increment total working days for each non-Saturday and non-public holiday
            $totalWorkingDays++;
        }

        $currentDate = strtotime('+1 day', $currentDate);
    }

    return $totalWorkingDays;
}

$firstDayOfMonth = date('Y-m-01'); 
$lastDayOfMonth = date('Y-m-t');  

// Define $publicHolidays array
$publicHolidays = array(
    '2023-01-01', 
    '2023-07-04', 
    '2023-12-25',
    '2024-01-01',
);

// Additional public holidays if needed
// $publicHolidays = array_merge($publicHolidays, array('YYYY-MM-DD', 'YYYY-MM-DD'));

if ($_SESSION['ROLE'] == 1) {
    if (isset($_POST['submit'])) {
        $employeeId = $_POST['employeeId'];
        $employeeName = $_POST['employeeName'];
        $salary = $_POST['salary'];

        // Check if it's the end of the month (from 28th to the last day)
        $currentDay = date('j'); 
        if ($currentDay >= 3) {
           
            $existingSql = "SELECT COUNT(*) as count FROM salary 
                            WHERE employee_id='$employeeId' 
                            AND (salary_month BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth')";
            $existingRes = mysqli_query($con, $existingSql);
            $existingRow = mysqli_fetch_assoc($existingRes);
            $count = $existingRow['count'];

            if ($count == 0) {
                
                $sqlLeave = "SELECT * FROM `leave` WHERE employee_id='$employeeId' AND leave_from BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
                $resLeave = mysqli_query($con, $sqlLeave);

                $totalLeaveDays = 0;
                $extraLeaveDays = 0; 

                while ($row = mysqli_fetch_assoc($resLeave)) {
                    $leaveFrom = strtotime($row['leave_from']);
                    $leaveTo = strtotime($row['leave_to']);

                    
                    for ($i = $leaveFrom; $i <= $leaveTo; $i = strtotime('+1 day', $i)) {
                        $currentDateString = date('Y-m-d', $i);

                        if (date('N', $i) != 6 && !in_array($currentDateString, $publicHolidays)) {
                            $totalLeaveDays++;
                        }

                     
                        if ($i > strtotime($lastDayOfMonth) && !in_array($currentDateString, $publicHolidays)) {
                           
                            $leaveTypeId = $row['leave_type_id'];
                            $assignedLeaveDays = getAssignedLeaveDays($employeeId, $leaveTypeId);
                            
                            if ($totalLeaveDays > $assignedLeaveDays) {
                                $extraLeaveDays++;
                            }
                        }
                    }
                }

                $saturdayCount = 0;
                $publicHolidayCount = 0;
                $currentDate = strtotime($firstDayOfMonth);

                while ($currentDate <= strtotime($lastDayOfMonth)) {
                    $currentDateString = date('Y-m-d', $currentDate);

                    if (date('N', $currentDate) == 6) { 
                        $saturdayCount++;
                    }

                    //
                    if (in_array($currentDateString, $publicHolidays)) {
                        $publicHolidayCount++;
                    }

                    $currentDate = strtotime('+1 day', $currentDate);
                }

                $effectiveWorkingDays = (strtotime($lastDayOfMonth) - strtotime($firstDayOfMonth)) / (60 * 60 * 24) + 1 - $saturdayCount - $publicHolidayCount;

                $effectiveWorkingDays -= $totalLeaveDays;

                $effectiveWorkingDays -= $extraLeaveDays;

                $monthlySalary = $salary;
                $dailySalary = $effectiveWorkingDays > 0 ? $monthlySalary / $effectiveWorkingDays : 0;

                $salaryAfterLeaveDeduction = $monthlySalary - ($totalLeaveDays * $dailySalary);

                $insertSql = "INSERT INTO salary (employee_id, employee_name, monthly_salary, daily_salary, total_working_days, total_leave_days, effective_working_days, calculated_salary, salary_month) 
                              VALUES ('$employeeId', '$employeeName', '$monthlySalary', '$dailySalary', '$effectiveWorkingDays', '$totalLeaveDays', '$effectiveWorkingDays', '$salaryAfterLeaveDeduction', '$firstDayOfMonth')";
                mysqli_query($con, $insertSql);

                header('Location: salary_details.php');
                exit();
            } else {
                $error = "Salary for this month has already been calculated.";
            }
        } else {
            $error = "Salary calculation is allowed only at the end of the month (from 28th to the last day).";
        }
    } else {
        $employeeId = '';
        $employeeName = '';
        $salary = '';
    }
} else {
    header('Location: view_salary.php');
    exit();
}

if (isset($_POST['submit'])) {
    if (isset($error)) {
        echo '<div class="error-message">' . $error . '</div>';
    }
}

function getAssignedLeaveDays($employeeId, $leaveTypeId) {
    // Implement the logic to fetch assigned leave days from the database based on employeeId and leaveTypeId
    // Return the assigned leave days
    // Example: 
    // $assignedLeaveDays = 10; 
    // return $assignedLeaveDays;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Salary Calculation</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
            /* Add more styles as needed */
        }

        .salary-container {
            margin-top: 120px;
            max-width: 800px;
            margin-left: 500px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .salary-container h2 {
            margin: 0 0 20px;
            color: #333;
        }

        .salary-container label {
            display: inline-block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .salary-container input[type="text"],
        .salary-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .salary-container input[type="submit"] {
            background-color: #929ABF;
            color: #fff;
            cursor: pointer;
        }

        .salary-container input[type="submit"]:hover {
            background-color: #6e79aa;
        }

        .salary-container p {
            margin: 0;
            font-weight: bold;
        }

        .salary-container .error {
            color: red;
            margin-top: 10px;
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
            justify-content: space-between; /* Push "View Details" to the right */
            width: 100%; /* Ensure header takes the full width */
        }

        .header h2 {
            margin: 0;
        }

        .header a {
            background-color:
            #929ABF ;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .header a:hover {
            background-color: #6e79aa;
        }
    </style>
</head>
<body>
<div class="nav">
    <h1>Salary</h1>
</div>
<div class="salary-container">
    <div class="header">
        <h2>Monthly Salary Calculation</h2>
        <a href="salary_details.php">View Details</a>
    </div>
    <form method="POST">
        <label for="employeeId">Employee ID:</label>
        <input type="text" name="employeeId" id="employeeId" value="<?php echo $employeeId; ?>" required>
        <br>
        <label for="employeeName">Employee Name:</label>
        <input type="text" name="employeeName" id="employeeName" value="<?php echo $employeeName; ?>" required>
        <br>
        <label for="salary">Monthly Salary:</label>
        <input type="text" name="salary" id="salary" value="<?php echo $salary; ?>" required>
        <br>
        <input type="submit" name="submit" value="Calculate Salary">
        <?php if (isset($_POST['submit']) && empty($error)) { ?>
            <form method="POST" action="salary_details.php" style="display: inline-block;">
                <input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>">
                <input type="submit" name="details" value="View Salary Details">
            </form>
        <?php } ?>
    </form>
    <?php if (isset($_POST['submit'])): ?>
        <?php if (isset($error) && $error != ''): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php else: ?>
            <p>Effective Working Days: <?php echo $effectiveWorkingDays; ?></p>
            <p>Total Working Days: <?php echo getTotalWorkingDaysInMonth($firstDayOfMonth, $lastDayOfMonth, $publicHolidays); ?></p>
            <p>Total Leave Days: <?php echo $totalLeaveDays; ?></p>
            <p>Extra Leave Days: <?php echo $extraLeaveDays; ?></p>
            <p>Daily Salary: Rs.<?php echo $dailySalary; ?></p>
            <p>Monthly Salary after Leave Deduction: Rs.<?php echo $salaryAfterLeaveDeduction; ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>

<?php
ob_end_flush();

require('footer.inc.php');
?>
