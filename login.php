<?php

require('db.inc.php');
$msg = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $role = $_POST['role'];

    $query = "SELECT * FROM employee WHERE email='$email' AND password='$password' AND role='$role'";
    $res = mysqli_query($con, $query);
    $count = mysqli_num_rows($res);

    if ($count > 0) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['ROLE'] = $row['role'];
        $_SESSION['USER_ID'] = $row['id'];
        $_SESSION['USER_NAME'] = $row['name'];

        if ($role === '1') {
            header('Location: dashboard.php');
            exit();
        } elseif ($role === '2') {
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $msg = "Please enter correct login details";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
      <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/table.css">
      <style>
   

.navbar {
  background-color: #757FAD;
  padding: 30px;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
}

.navbar-list {
  list-style-type: none;
 
  margin: 0;
  padding: 0; 
  display: flex;
  justify-content: center; 
  align-items: center;
}

.navbar-link {
  text-decoration: none;
  color: #333;
  margin-right: 10px;
  padding: 10px; 
  font-size: 20px; 
   font-family: serif;
}

.navbar-link.active {
  font-weight: bold;
}
    </style>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function selectRole() {
            var role = document.getElementById('role').value;
            var loginButton = document.getElementById('login-button');
            
            if (role === '1') {
                loginButton.value = 'Login as Admin';
            } else if (role === '2') {
                loginButton.value = 'Login as Employee';
            }
        }
    </script>
</head>
<body>
    <!-- <nav class="navbar">
        <ul class="navbar-list">
            <li><a class="navbar-link" href="home.php">Home</a></li>
            <li><a class="navbar-link active" href="login.php">Login</a></li>
        </ul>
    </nav> -->

    <div class="login-container">
        <h1 class="login-title">Login</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="login-form">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div>
                <label for="role">Role</label>
                <select id="role" name="role" onchange="selectRole()">
                    <option value="1">Admin</option>
                    <option value="2">Employee</option>
                </select>
            </div>

            <input type="submit" name="login" id="login-button" value="Login" class="login-button">
        </form>

        <?php if (!empty($msg)) { ?>
            <p><?php echo $msg; ?></p>
        <?php } ?>
    </div>
</body>
</html>


check