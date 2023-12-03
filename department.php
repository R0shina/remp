<?php
ob_start();

require('top.inc.php');

if ($_SESSION['ROLE'] != 1) {
   header('location:add_employee.php?id=' . $_SESSION['USER_ID']);
   die();
}

if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
   $id = mysqli_real_escape_string($con, $_GET['id']);
   mysqli_query($con, "delete from department where id='$id'");
}

$res = mysqli_query($con, "select * from department order by id desc");

?>

<!DOCTYPE html>
<html>
   <head>
      <title>Department Page</title>
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
         <h1>Department</h1>
      </div>
      <div class="content pb-0">
         <div class="orders">
            <div class="row">
               <div class="col-xl-12">
                  <div class="card">
                     <div class="card-body">
                        
                        <h2 class="box_title_link" ><a href="add_department.php">Add Department</a></h2>
                     </div>
                     <div class="card-body--">
                        <div class="table-wrapper">
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th width="10%">S.No</th>
                                    <th width="10%">ID</th>
                                    <th width="70%">Department Name</th>
                                    <th width="20%"></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php 
                                 $i=1;
                                 if ($res && mysqli_num_rows($res) > 0) { // Check if the result is not null and has rows
                                    while($row=mysqli_fetch_assoc($res)){?>
                                    <tr>
                                       <td><?php echo $i?></td>
                                       <td><?php echo isset($row['id']) ? $row['id'] : ''?></td>
                                       <td><?php echo isset($row['department']) ? $row['department'] : ''?></td>
                                       <td>
                                          <a href="add_department.php?id=<?php echo isset($row['id']) ? $row['id'] : ''?>">Edit</a> |
                                          <a href="department.php?id=<?php echo isset($row['id']) ? $row['id'] : ''?>&type=delete">Delete</a>
                                       </td>
                                    </tr>
                                    <?php 
                                    $i++;
                                    }
                                 } else {
                                    echo '<tr><td colspan="4">No departments found</td></tr>';
                                 }
                                 ?>
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
