<?php
ob_start();
require('top.inc.php');


if ($_SESSION['ROLE'] != 1) {
    header('location: add_employee.php?id=' . $_SESSION['USER_ID']);
    die();
}

include "./file.php";

if (isset($_POST['assign_project'])) {
    $project_name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    

    list($start_date, $end_date) = explode(" - ", mysqli_real_escape_string($con, $_POST['daterange']));

    $employee_id = mysqli_real_escape_string($con, $_POST['employee_id']);
    $employee_name = mysqli_real_escape_string($con, $_POST['employee_name']);


    $existing_project = mysqli_query($con, "SELECT id FROM projects WHERE name = '$project_name'");

    if ($existing_project && mysqli_num_rows($existing_project) > 0) {
        $project = mysqli_fetch_assoc($existing_project);
        $project_id = $project['id'];
    } else {
     
        $sql_insert_project = "INSERT INTO projects (name, description, start_date, end_date) 
                               VALUES ('$project_name', '$description', '$start_date', '$end_date')";
        $result_insert_project = mysqli_query($con, $sql_insert_project);

        if (!$result_insert_project) {
            echo 'Error adding project: ' . mysqli_error($con);
        }

        $project_id = mysqli_insert_id($con);
    }

  
    $sql_assign = "INSERT INTO project_assignments (project_id, employee_id, employee_name) 
                   VALUES ('$project_id', '$employee_id', '$employee_name')";
    $result_assign = mysqli_query($con, $sql_assign);

    // if (!$result_assign) {
    //     echo 'Error assigning project: ' . mysqli_error($con);
    // }

    if ($result_assign) {
    // Successful assignment, redirect to project_assignments.php
    header('Location: project_assignments.php');
    exit(); // Ensure that no further code is executed after the redirect
} else {
    echo 'Error assigning project: ' . mysqli_error($con);
}

}

$projects_res = mysqli_query($con, "SELECT id, name FROM projects");
$employees_res = mysqli_query($con, "SELECT id, name FROM employee");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Project Page</title>
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        } */

       .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            left:250px;
            background-color: white;
            padding: 20px;
            padding-top:30px;
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
             margin-top: 130px;
            margin-left: 280px;
            padding: 20px;
            padding-left : 20px;
            padding-right : 20px;
            box-sizing: border-box;
            background-color: #f5f5f5; 
             border-radius: 5px 
         }

        form {
            display: grid;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .header{
            text-align : right;
        }

        .header a {
    background-color: #929ABF;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.header a:hover {
    background-color:#6e79aa;
}


        .btn-info {
            background-color: #929ABF;
            color: #fff;
            font-size: 18px;
            padding: 10px;
            border: 1px solid #6e79aa;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-info:hover {
            background-color: #6e79aa;
        }

        .btn-block {
            width: 100%;
        }
    </style>
</head>

   <div class="nav">
      <h1>Task Assign</h1>
      <!-- <button onclick="printData()">Print</button> -->
    </div>

    <div class="content">
                      <div class="header"> <a href="project_assignments.php">View Details</a></div>

        <form method="post" action="">
            <!-- <label for="project_id">Select Project:</label> -->
      
<label for="name">Project Name:</label>
<input type="text" name="name" required>


<label for="description">Description:</label>
<textarea name="description" required></textarea>


<label for="daterange">Select Date Range:</label>
<input type="text" name="daterange" required readonly>


<!-- <label for="status">Status:</label>
<input type="text" name="status" required> -->


<label for="employee_id">Assign to Employee:</label>
<select name="employee_id" required>
    <?php while ($employee = mysqli_fetch_assoc($employees_res)) : ?>
        <option value="<?= $employee['id']; ?>"><?= $employee['name']; ?></option>
    <?php endwhile; ?>
</select>


            <input type="hidden" name="employee_name" id="employee_name">

              <button  type="submit" name = "assign_project" class="btn btn-lg btn-info btn-block">
							   <span id="payment-button-amount">Assign Project</span>
							   </button>

        </form>
    </div>

    <script>
       
        document.addEventListener("DOMContentLoaded", function () {
            const employeeSelect = document.querySelector('select[name="employee_id"]');
            const employeeNameField = document.querySelector('input[name="employee_name"]');

            employeeSelect.addEventListener('change', function () {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                employeeNameField.value = selectedOption ? selectedOption.text : '';
            });
        });


   $(function() {
        $('input[name="daterange"]').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            opens: 'left',
            minDate: moment() 
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    </script>
</body>

</html>