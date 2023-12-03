<?php
ob_start();

require('top.inc.php');
$cur_year = date("Y");
$prev_year =$cur_year-1;
$next_year =$cur_year+1;

function getTotalLeaveCountOfYear($sick_leave_type_id, $user_id){
   global $con;
    $count = 0;
    $sql = "SELECT sum(leave_to-leave_from+1) as count FROM `leave` WHERE leave_id = $sick_leave_type_id AND employee_id = $user_id and leave_status=2" ;
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)) {
      if (isset($row['count']))
        $count = $row['count'];
    }
    return $count;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
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

         .content {
            margin-top: 30px;
            margin-left: 250px;
            padding: 10px;
            box-sizing: border-box;
         }
      </style>

  </head>
  <body>
     <div class="nav">
         <h1>My Leave</h1>
      </div>
    <div class="content pb-0">
      <div class="orders">
        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-body">
                <?php if($_SESSION['ROLE']==2){ ?>
                  <h4 class="box_title_link">
                    <a href="add_leave.php" width="15%">Add Leave</a> |
                    <a href="my leave.php">My Leave</a>
                  </h4>
                <?php } ?>
              </div>

              <h4 class="box-title">Leave List</h4>
              <div class="card-body--">
                <div class="table-stats order-table ov-h">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Leave Type</th>
                        <th>Remaining Leave</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Sick Leave</td>
                        <td>
                          <?php
                            $user_id = $_SESSION["USER_ID"];
                            echo getTotalLeaveCountOfYear(1, $user_id)."/12";
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td>Casual Leave</td>
                        <td>
                          <?php
                            $user_id = $_SESSION["USER_ID"];
                            echo getTotalLeaveCountOfYear(2, $user_id)."/18";
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td>Maternity Leave</td>
                        <td>
                          <?php
                            $user_id = $_SESSION["USER_ID"];
                            echo getTotalLeaveCountOfYear(4, $user_id)."/60";
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td>Paternity Leave</td>
                        <td>
                          <?php
                            $user_id = $_SESSION["USER_ID"];
                            echo getTotalLeaveCountOfYear(6, $user_id)."/14";
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td>Other Leave</td>
                        <td>
                          <?php
                            $user_id = $_SESSION["USER_ID"];
                            echo getTotalLeaveCountOfYear(8, $user_id)."/10";
                          ?>
                        </td>
                      </tr>
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
