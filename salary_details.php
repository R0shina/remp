<?php
ob_start();
require('top.inc.php');
include "./file.php";


if (isset($_GET['deleteId'])) {
    $deleteEmployeeId = $_GET['deleteId'];

    // Perform the delete operation based on $deleteEmployeeId
    $deleteSql = "DELETE FROM salary WHERE employee_id = $deleteEmployeeId";
    
    if (mysqli_query($con, $deleteSql)) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }

    exit();
}


// Pagination
$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

// Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$searchCondition = $search ? "AND employee_name LIKE '%$search%'" : '';

// Month filter
// $selectedMonth = isset($_GET['month']) ? mysqli_real_escape_string($con, $_GET['month']) : date('m');
// $monthFilter = "AND MONTH(salary_month) = $selectedMonth";

// // If selected month is the current month, include records with salary_month up to the current date
// if ($selectedMonth == date('m')) {
//     $monthFilter .= " AND salary_month <= NOW()";
// }


$selectedMonth = isset($_GET['month']) ? mysqli_real_escape_string($con, $_GET['month']) : date('m');
$currentYear = date('Y');
$monthFilter = "AND YEAR(salary_month) = $currentYear AND MONTH(salary_month) = $selectedMonth";


// $selectSql = "SELECT * FROM salary WHERE 1 $searchCondition $monthFilter LIMIT $offset, $perPage";



// Retrieve the employee details and salary calculations from the database with pagination, search, and month filter
$selectSql = "SELECT * FROM salary WHERE 1 $searchCondition $monthFilter LIMIT $offset, $perPage";
$result = mysqli_query($con, $selectSql);

// Get the total number of rows for pagination
$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM salary WHERE 1 $searchCondition $monthFilter"));
$totalPages = ceil($totalRows / $perPage);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Employee Salary Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="script.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .content-container {
            display: flex;
            justify-content: flex-end;
        }

        .salary-table {
            width: 1200px;
            margin-top: 50px;
            margin-right: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .salary-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-table th,
        .salary-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .salary-table th {
            background-color: #f5f5f5;
        }

        .salary-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin: 0 5px;
        }
.sub-menu {
    display: flex;
    justify-content: space-between;
}

.search-bar {
    margin-bottom: 20px;
    display: flex; /* Make the search bar a flex container */
    align-items: center; /* Center items vertically */
}

.search-bar input[type="text"] {
    padding: 8px;
    width: 250px; /* Increase the width of the input */
    margin-right: 10px; /* Add some right margin for separation */
}

.search-bar button {
    padding: 8px;
    background-color: #929ABF;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.month-filter {
    margin-bottom: 20px;
}

.month-filter select {
    padding: 8px;
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

         .edit-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .edit-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }


.edit-modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .edit-modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    .close {
        /* color: #aaa; */
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    h2 {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 16px;
        box-sizing: border-box;
    }

    button {
        background-color: #929ABF;
        /* color: white; */
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #6e79aa;
    }

    .no-data-message {
    text-align: center;
    padding: 20px;
    font-size: 16px;
    color: #888;
}



    </style>
</head>

<body>
    <div class="nav">
        <h1>Salary Details</h1>
    </div>
    <div class="content-container">
        <div class="salary-table">
            <h2>Employee Salary Details</h2>
           <div  class="sub-menu">
        
            <div class="month-filter">
                <form action="" method="get">
                    <label for="month">Select Month:</label>
                    <select name="month" id="month">
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $selected = ($selectedMonth == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Apply</button>
                </form>
            </div>  

               <div class="search-bar">
                <form action="" method="get">
                    <input type="text" name="search" placeholder="Search by Employee Name" value="<?php echo $search; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
            <table>
       <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Monthly Salary</th>
                        <th>Daily Salary</th>
                        <th>Total Working Days</th>
                        <th>Total Leave Days</th>
                        <th>Effective Working Days</th>
                        <th>Calculated Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['employee_id']; ?></td>
                                <td id="employeeName_<?php echo $row['employee_id']; ?>"><?php echo $row['employee_name']; ?></td>
                                <td id="monthlySalary_<?php echo $row['employee_id']; ?>"><?php echo $row['monthly_salary']; ?></td>
                                <td><?php echo $row['daily_salary']; ?></td>
                                <td><?php echo $row['total_working_days']; ?></td>
                                <td><?php echo $row['total_leave_days']; ?></td>
                                <td><?php echo $row['effective_working_days']; ?></td>
                                <td><?php echo $row['calculated_salary']; ?></td>
                              <td>
    <?php
    $currentMonth = date('m');
    if ($selectedMonth == $currentMonth) {
    ?>
        <button onclick="openEditPopup(<?php echo $row['employee_id']; ?>, '<?php echo $row['employee_name']; ?>', <?php echo $row['monthly_salary']; ?>)">Edit</button>
    <?php
    }
    ?>
<button onclick="confirmAndDelete(<?php echo $row['employee_id']; ?>, <?php echo $selectedMonth; ?>)">Delete</button>

</td>

                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
            <td colspan="9">
                <div class="no-data-message">No salary details found for the selected Month.</div>
            </td>
        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <div class="pagination">
                    <?php if ($currentPage > 1) { ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo $search; ?>&month=<?php echo $selectedMonth; ?>">Previous</a>
                    <?php } ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&month=<?php echo $selectedMonth; ?>" <?php echo ($i == $currentPage) ? 'style="background-color: #333;"' : ''; ?>><?php echo $i; ?></a>
                    <?php } ?>
                    <?php if ($currentPage < $totalPages) { ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo $search; ?>&month=<?php echo $selectedMonth; ?>">Next</a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>


      <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <span class="close" onclick="closeEditPopup()">&times;</span>
            <h2>Edit Employee Details</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="editEmployeeId" id="editEmployeeId" value="">
                <label for="editEmployeeName">Employee Name:</label>
                <input type="text" name="editEmployeeName" id="editEmployeeName" required>
                <label for="editMonthlySalary">Monthly Salary:</label>
                <input type="text" name="editMonthlySalary" id="editMonthlySalary" required>
<form id="editForm" onsubmit="saveEdit(); return false;">
    <button type="submit" name="saveEdit">Save Changes</button>
</form>
            </form>
        </div>
    </div>

    <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveEdit'])) {
    // Process form submission and update the database
    $editEmployeeId = $_POST['editEmployeeId'];
    $editEmployeeName = mysqli_real_escape_string($con, $_POST['editEmployeeName']);
    $editMonthlySalary = mysqli_real_escape_string($con, $_POST['editMonthlySalary']);

    $updateSql = "UPDATE salary SET employee_name = '$editEmployeeName', monthly_salary = '$editMonthlySalary' WHERE employee_id = $editEmployeeId";
    mysqli_query($con, $updateSql);

  $selectUpdatedDataSql = "SELECT * FROM salary WHERE employee_id = $editEmployeeId";
    $updatedData = mysqli_query($con, $selectUpdatedDataSql);
    $updatedRow = mysqli_fetch_assoc($updatedData);

    // Redirect to another page
    header('Location: salary_details.php'); // Replace with the actual page you want to redirect to
    exit(); // Ensure that no further code is executed on this page
}

?>


<script>

  function confirmAndDelete(employeeId) {
  if (confirm("Are you sure you want to delete this record?")) {
    // Send an AJAX request to delete the record
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          // Reload the page or update the table as needed
          window.location.reload(); // This will reload the current page
        } else {
          console.error("Error deleting record. Status: " + xhr.status);
        }
      }
    };

    xhr.open("GET", "salary_details.php?deleteId=" + employeeId, true);
    xhr.send();
  }
}

    </script>

</body>

</html>

<?php
ob_end_flush();
require('footer.inc.php');
?>
