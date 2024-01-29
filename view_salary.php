<?php
ob_start();

require('top.inc.php');
include "./file.php";

function getTotalLeaveCountOfYear($sick_leave_type_id, $user_id){
    global $con;
    $count = 0;
    $sql = "SELECT sum(leave_to-leave_from+1) as count FROM `leave` WHERE leave_id = $sick_leave_type_id AND employee_id = $user_id and leave_status=2";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)) {
        if (isset($row['count']))
            $count = $row['count'];
    }
    return $count;
}


if ($_SESSION['ROLE'] != 1) {
    $eid = $_SESSION['USER_ID'];

    // Step 1: Determine the daily salary
    $dailySalary = 100; // Example daily salary, replace with actual calculation or retrieval from database

    // Step 2: Retrieve leave history
    $sql = "SELECT sum(leave_to-leave_from+1) as leave_days, `leave`.*, employee.name ,employee.id as eid 
            FROM `leave`,employee 
            WHERE `leave`.employee_id='$eid' AND `leave`.employee_id=employee.id 
            GROUP BY leave_from, leave_to 
            ORDER BY `leave`.id DESC";
    $res = mysqli_query($con, $sql);

    // Step 3: Calculate total leave days
    $totalLeaveDays = 0;
    while($row = mysqli_fetch_assoc($res)){
        $leaveFrom = strtotime($row['leave_from']);
        $leaveTo = strtotime($row['leave_to']);
        $leaveDuration = ($leaveTo - $leaveFrom) / (60 * 60 * 24) + 1; // Add 1 to include both the start and end dates of leave
        $totalLeaveDays += $leaveDuration;
    }

    // Step 4: Calculate effective working days
    $totalWorkingDays = date('t'); // Total number of days in the current month
    $effectiveWorkingDays = $totalWorkingDays - $totalLeaveDays;

    // Step 5: Calculate salary
    $salary = $effectiveWorkingDays * $dailySalary;
} else {
 
    header('Location: salary.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>View Monthly Salary</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/salary.css">

    <style>
      /* body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 0;
      } */

      .salary-container {
        max-width: 1200px;
        margin: 50px auto ;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding-left :20px;
        margin-right: 45px; 
        margin-top : 120px;
      }

      .salary-container h2 {
        margin: 0 0 20px;
        color: #333;
      }

      .salary-container table {
        width: 100%;
        border-collapse: collapse;
        max-width: none;
        
      }

      .salary-container th,
      .salary-container td {
      padding: 12px; 
        text-align: left;
        border-bottom: 1px solid #ddd;
      }

      .salary-container th {
        background-color: #f5f5f5;
      }

      .salary-container tr:nth-child(even) {
        background-color: #f9f9f9;
      }

      	.nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left:250px;
            background-color: white;
            padding: 20px;
             padding-top:20px;
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
      <h1>Salary</h1>
    </div>
    <div class="salary-container">
      <h2>View Monthly Salary</h2>
      <?php if($_SESSION['ROLE'] == 2) { ?>
   <table>
    <tr>
        <th>Date</th>
        <th>Total Working Days</th>
        <th>Total Leave Days</th>
        <th>Effective Working Days</th>
        <th>Daily Salary</th>
        <th>Monthly Salary</th>
           <th>Total Salary</th>

    </tr>
    <?php
    // Get the current month
    $currentMonth = date('Y-m');
    
    // Retrieve salary history for all months
    $salaryHistorySql = "SELECT * FROM salary WHERE employee_id = $eid ORDER BY salary_month DESC";
    $salaryHistoryResult = mysqli_query($con, $salaryHistorySql);

    while ($historyRow = mysqli_fetch_assoc($salaryHistoryResult)) {
        echo "<tr>";
        echo "<td>" . date('F Y', strtotime($historyRow['salary_month'])) . "</td>";
        echo "<td>" . $historyRow['total_working_days'] . "</td>";
        echo "<td>" . $historyRow['total_leave_days'] . "</td>";
        echo "<td>" . $historyRow['effective_working_days'] . "</td>";
        echo "<td>Rs." . $historyRow['daily_salary'] . "</td>";
        echo "<td>Rs." . $historyRow['monthly_salary'] . "</td>";
          echo "<td>Rs." . $historyRow['calculated_salary'] . "</td>";
  
        echo "</tr>";
    }
    ?>
</table>


        
      <?php } else { ?>
        <p>Access Denied. Only regular employees can view their salary.</p>
      <?php } ?>
    </div>
  </body>
</html>

<?php
ob_end_flush(); 

require('footer.inc.php');
?>
