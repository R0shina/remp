<?php
session_start();

if (isset($_POST['logout'])) {

    $confirmation = $_POST['confirmation'];
    if ($confirmation === 'yes') {
        unset($_SESSION['ROLE']);
        unset($_SESSION['USER_ID']);
        unset($_SESSION['USER_NAME']);
        header('location: login.php');
        die();
    } else {
      
        $message = "Logout cancelled.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout Page</title>
    <style>
  
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
            padding: 20px;
            border: 1px solid #888;
            width: 25%;
            text-align: center;
        }
        .modal-buttons {
            margin-top: 20px;
             background-color: #929ABF;
        }

      
    button:hover {
        background-color: #6e79aa;
    }
    </style>
    <script>
        function openModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
        }

        function closeModal(confirmed) {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";

            if (confirmed) {
                document.getElementById("confirmation").value = 'yes';
                document.forms["logoutForm"].submit();
            }
        }
    </script>
</head>
<body>
    
    <button class= "logout" onclick="openModal()">Logout</button>

    <!-- Logout Confirmation Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to logout?</p>
            <div class="modal-buttons">
                <button onclick="closeModal(true)">Yes</button>
                <button onclick="closeModal(false)">No</button>
            </div>
        </div>
    </div>

    <!-- Add a form for the logout action -->
    <form method="POST" action="" id="logoutForm">
        <input type="hidden" id="confirmation" name="confirmation" value="">
        <input type="hidden" name="logout">
    </form>

    <?php
    if (isset($message)) {
        echo "<p>$message</p>";
    }
    ?>
    
    <!-- Your HTML content here -->
</body>
</html>
