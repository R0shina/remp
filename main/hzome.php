<!DOCTYPE html>
<html>
<head>
  <title>Home Page</title>
 <style>
  .navbar {
    background-color: #757FAD;
    padding: 50px; /* Adjust the padding to change the size of the navbar */
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
</head>

<body>
  <nav class="navbar">
    <ul class="navbar-list">
      <li><a class="navbar-link active" href="home.php">Home</a></li>
      <li><a class="navbar-link" href="login.php">Login</a></li>
    </ul>
  </nav>

  <div class="content">
    <h1 class="content-title">Welcome to the Home Page</h1>
    <p class="content-description">This is the content of the home page.</p>
  </div>
</body>
</html>
