<?php
ob_start();

require('top.inc.php');
if($_SESSION['ROLE']!=1){
	header('location:add_employee.php?id='.$_SESSION['USER_ID']);
	die();
}
if(isset($_GET['type']) && $_GET['type']=='delete' && isset($_GET['id'])){
	$id=mysqli_real_escape_string($con,$_GET['id']);
	mysqli_query($con,"delete from leave_type where id='$id'");
}
$res=mysqli_query($con,"select * from leave_type order by id desc");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">
</head>
<body>
    <div>
        <h1 class="nav">Leave Type</h1>
        </div>
<div class="content pb-0">
  
                    <div class="card-body">
                        
                        <h2 class="box_title_link"><a href="add_leave_type.php">Add Leave Type</a></h2>
                    </div>
                
                            <table class="table">
                                <thead>
                                <tr>
                                    <th width="5%">S.No</th>
                                    <th width="5%">ID</th>
                                    <th width="40%">Leave Type</th>
                                        <th width="20%"> Total Leave</th>
                                    <th width="20%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($res)) { ?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo $row['id'] ?></td>
                                        <td><?php echo $row['leave_type'] ?></td>
                                                                <td><?php echo $row['max_days_per_year'] ?></td>

                                        <td>
                                            <a href="add_leave_type.php?id=<?php echo $row['id'] ?>">Edit</a> |
                                            <a href="leave_type.php?id=<?php echo $row['id'] ?>&type=delete">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                } ?>
                                </tbody>
                            </table>
                     
</div>
</body>
</html>

              
<?php
ob_end_flush(); 
require('footer.inc.php');
?>