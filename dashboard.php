<?php
ob_start();
require('top.inc.php');
ob_end_flush(); 

?>

<!DOCTYPE html>
<html>
<head>
   <title>Dashboard Page</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <!-- <link rel="stylesheet" type="text/css" href="css/table.css"> -->
   <style>
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

      .nav input[type="date"] {
         border: none;
         background-color: transparent;
         font-size: 18px;
         font-family: Arial, sans-serif;
         color: #333;
         padding: 5px;
      }

      .nav .current-date {
         font-size: 18px;
         font-weight: bold;
      }

      .content {
         margin-top: 100px;
         margin-left: 250px;
         padding: 10px;
         box-sizing: border-box;
         
      }
      
      .thumbnail-container {
         display: flex;
         flex-wrap: wrap;
         justify-content: center;
      }

      .thumbnail-link {
         text-decoration: none;
         color: inherit;
         margin: 10px;
      }

      .thumbnail {
         width: 350px; /* Set the width of the thumbnail */
         height: 300px; /* Set the height of the thumbnail */
         background-color: #f1f1f1;
         border-radius: 5px;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         padding: 10px;
         transition: transform 0.3s ease;
      }

      .thumbnail:hover {
         transform: scale(1.05); /* Add a scale effect on hover */
      }

      .thumbnail-icon {
         font-size: 60px;
         margin-bottom: 10px;
      }

      .thumbnail-title {
         font-size: 16px;
         font-weight: bold;
         text-align: center;
      }
   </style>
</head>
<body>
   <div class="nav">
      <h1>Dashboard</h1>
      <span class="current-date"><?php echo date('F j, Y'); ?></span>
   </div>

   <div class="content">
      <div class="thumbnail-container">
         <?php if ($_SESSION['ROLE'] == 1) { ?>
            <a href="department.php" class="thumbnail-link">
               <div class="thumbnail">
                  <div class="thumbnail-icon">
                     <i class="fas fa-building"></i>
                  </div>
                  <div class="thumbnail-title">Department</div>
               </div>
            </a>

            <a href="leave_type.php" class="thumbnail-link">
               <div class="thumbnail">
                  <div class="thumbnail-icon">
                     <i class="fas fa-clipboard"></i>
                  </div>
                  <div class="thumbnail-title">Leave Type</div>
               </div>
            </a>

            <a href="admin_project.php" class="thumbnail-link">
            <div class="thumbnail">
               <div class="thumbnail-icon">
                  <i class="fa fa-tasks"></i>
               </div>
               <div class="thumbnail-title">Task</div>
            </div>
         </a>
         
         <a href="employee.php" class="thumbnail-link">
            <div class="thumbnail">
               <div class="thumbnail-icon">
                  <i class="fas fa-users"></i>
               </div>
               <div class="thumbnail-title">Employee</div>
            </div>
         </a>
         <?php } ?>


         
        

         <?php if ($_SESSION['ROLE'] == 1 || $_SESSION['ROLE'] == 2) { ?>
            <a href="leave.php" class="thumbnail-link">
               <div class="thumbnail">
                  <div class="thumbnail-icon">
                     <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="thumbnail-title">Leave</div>
               </div>
            </a>

            <a href="salary.php" class="thumbnail-link">
               <div class="thumbnail">
                  <div class="thumbnail-icon">
                     <i class="fas fa-money-bill-alt"></i>
                  </div>
                  <div class="thumbnail-title">Salary</div>
               </div>
            </a>

             <!-- <a href="assign_project.php" class="thumbnail-link">
            <div class="thumbnail">
               <div class="thumbnail-icon">
                  <i class="fas fa-clock"></i>
               </div>
               <div class="thumbnail-title">Role</div>
            </div>
         </a> -->

         <a href="my_assignments.php" class="thumbnail-link">
            <div class="thumbnail">
               <div class="thumbnail-icon">
                  <i class="fa fa-tasks"></i>
               </div>
               <div class="thumbnail-title">My Task</div>
            </div>
         </a>
         <?php } ?>
      </div>
   </div>
</body>
</html>
