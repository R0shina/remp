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
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        table {
            border-collapse: collapse;
             width: 100%;
             border-collapse: collapse;
            border: 5px solid #e0e0e0;
        }

        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
              border-right: none;
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
    <div>
        <h1 class="nav">Leave Type</h1>
        </div>
<div class="content pb-0">
    <div class="orders">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        
                        <h2 class="box_title_link"><a href="add_leave_type.php">Add Leave Type</a></h2>
                    </div>
                    <div class="card-body--">
                        <div class="table-stats order-table ov-h">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th width="5%">S.No</th>
                                    <th width="5%">ID</th>
                                    <th width="70%">Leave Type</th>
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