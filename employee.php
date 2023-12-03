<?php
ob_start();
require('top.inc.php');
if ($_SESSION['ROLE'] != 1) {
    header('location:add_employee.php?id=' . $_SESSION['USER_ID']);
    die();
}
if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    mysqli_query($con, "DELETE FROM employee WHERE id='$id'");
}
$res = mysqli_query($con, "SELECT * FROM employee WHERE role=2 ORDER BY id DESC");
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
            border: 5px solid #e0e0e0;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .search-bar {
            display: flex;
            margin-bottom: 20px;
            padding-left:950px;
        }

        .search-bar input {
            flex: 1;
            padding: 5px;
            width: 50px;
            
        }

        .search-bar button {
            padding: 5px 10px;
            background-color: grey;
            color: white;
            border: none;
            cursor: pointer;
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
         <h1>Employee</h1>
      </div>
    <div class="content pb-0">
        <div class="orders">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                    <div class="card-body">
                        <h2 class="emp"><a href="add_employee.php">Add Employee</a> </h2>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search..." style="width: 100px;">
                    <button type="button" onclick="searchTable()">Search</button>
              
                
                                         
            </div>
                        </div>
                        <div class="card-body--">
                            <div class="table-stats order-table ov-h">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.No</th>
                                            <th width="5%">ID</th>
                                            <th width="12%">Name</th>
                                            <th width="10%">Email</th>
                                            <th width="5%">Address</th>
                                            <th width="5%">Department</th>
                                            <th width="5%">Mobile</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        while ($row = mysqli_fetch_assoc($res)) { ?>
                                            <tr>
                                                <td><?php echo $i ?></td>
                                                <td><?php echo $row['id'] ?></td>
                                                <td><?php echo $row['name'] ?></td>
                                                <td><?php echo $row['email'] ?></td>
                                                 <td><?php echo $row['address'] ?></td>
                                       <td><?php echo $row['department_id'] ?></td>

                                                <td><?php echo $row['mobile'] ?></td>
                                                <td>
                                                    <a href="add_employee.php?id=<?php echo $row['id'] ?>">Edit</a> |
                                                    <a href="employee.php?id=<?php echo $row['id'] ?>&type=delete">Delete</a>
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
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementsByTagName("table")[0];
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[2]; // Index 2 corresponds to the Name column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>

<?php
ob_end_flush(); 
require('footer.inc.php');
?>
