<?php
require('db.inc.php');
if (!isset($_SESSION['ROLE'])) {
    header('location: login.php');
    die();
}
if (isset($_POST['logout'])) {
    $confirmation = $_POST['confirmation'];
    if ($confirmation === 'yes') {
       
        session_start();
        unset($_SESSION['ROLE']);
        unset($_SESSION['USER_ID']);
        unset($_SESSION['USER_NAME']);
        session_destroy();
        header('location: login.php');
        exit();
    } else {
        $message = "Logout cancelled.";
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="sidebar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            margin: 0;
            background-color: #f9f9f9;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .logo-img {
            width: 100px;
            height: auto;
        }

        .sidebar .navbar-brand:hover .logo-img {
            pointer-events: none;
        }


.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 60px;
    border: 1px solid #888;
    max-width: 400px; /* Adjust the maximum width of the modal */
    width: 80%; /* Adjust the width of the modal */
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
}

.modal-buttons {
    margin-top: 20px;
    text-align: center;
}

.modal-button {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
    margin: 55px 100px;
    font-size: 24px; /* Adjust the font size of the buttons */
}
.modal-button:hover {
    background-color: #2980b9;
}

        
        .logout-link {
            margin-top: 20px;
            text-align: center;
        }

       .logout-button {
    background-color: #7C81AD;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
    position: fixed;
    bottom: 20px;
    /* right: 20px; */
    left : 50px;
    z-index: 999;
}

.logout-button:hover {
    background-color: #c0392b;
}



       
.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    max-width: 400px;
    width: 80%;
    padding: 20px;
    text-align: center;
    border-radius: 5px;
}

.popup p {
    font-size: 18px;
    margin-bottom: 20px;
}

.popup-buttons {
    display: flex;
    justify-content: center;
}

.popup-button {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 0 10px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.popup-button:hover {
    background-color: #2980b9;
}


    </style>
</head>
<body>

<div class="sidebar">
    <ul>
        <li>
            <a class="navbar-brand" href="dashboard.php">
                <img class="logo-img" src="logo.png" alt="Logo">
            </a>
        </li>
        <li>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        </li>
        <?php if ($_SESSION['ROLE'] == 1) { ?>
            <li>
                <a href="department.php"><i class="fas fa-building"></i>Department</a>
            </li>
            <li>
                <a href="leave_type.php"><i class="fas fa-clipboard"></i>Leave Type</a>
            </li>
            <li>
                <a href="employee.php"><i class="fas fa-users"></i>Employee</a>
            </li>

            <li>
            <a href="session.php"><i class="fas fa-clock"></i>Session</a>
        </li>

        <?php } else { ?>
            <li>
                <a href="add_employee.php?id=<?php echo $_SESSION['USER_ID'] ?>"><i class="fas fa-user"></i>Profile</a>
            </li>
        <?php } ?>
        <li>
            <a href="leave.php"><i class="far fa-calendar-alt"></i>Leave</a>
        </li>
        <li>
            <a href="salary.php"><i class="fas fa-money-bill-wave"></i>Salary</a>
        </li>

           
    </ul>


    <div class="logout-link">
        <button class="logout-button" onclick="openModal()"><i class="fas fa-power-off"></i>Logout</button>
    </div>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to logout?</p>
        <div class="modal-buttons">
            <button onclick="closeModal(true)">Yes</button>
            <button onclick="closeModal(false)">No</button>
        </div>
    </div>
</div>


<form method="POST" action="" name="logoutForm">
    <input type="hidden" id="confirmation" name="confirmation" value="">
    <input type="hidden" name="logout">
</form>

<?php
if (isset($message)) {
    echo "<p>$message</p>";
}
?>

<div class="content">
    <!-- Your dashboard content here -->
</div>


<script>
    function openModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    }

    function closeModal(confirmed) {
        var modal = document.getElementById("myModal");

        if (confirmed) {
            document.getElementById("confirmation").value = 'yes';
            document.forms["logoutForm"].submit();
        } else {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>
