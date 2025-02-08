<?php
require 'vendor/autoload.php'; // Make sure to install AWS SDK via Composer
session_start();
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

// Initialize DynamoDB Client
$dynamoDb = new DynamoDbClient([
    'region' => 'us-east-1', // Change this to your AWS region
    'version' => 'latest',
    'credentials' => [
        'key'    => 'AKIA5FTZDZNTZDIC4HFW',
        'secret' => 'dtPSaIB+BW6MEQjUNUu4q/3oSKTcNx/cjJtGeDf5',
    ],
]);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    try {
        $result = $dynamoDb->getItem([
            'TableName' => 'AccountsTable', // Your DynamoDB table
            'Key' => [
                'email' => ['S' => $email]
            ]
        ]);

        if (!isset($result['Item'])) {
            $error = "Invalid email or password.";
        } else {
            $storedPassword = $result['Item']['password']['S'];
            $subs = $result['Item']['subs']['S'];

            if (strcmp($password, $storedPassword)==0) {
                $_SESSION['email'] = $email;
                if(isset($subs)){
                $_SESSION['subs'] = $subs;
                }
                $success = "Login successful!";
                header("Location: dashboard.php"); // Redirect to dashboard on successful login
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        }
    } catch (AwsException $e) {
        $error = "Unable to connect to the database: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <!-- Bootstrap css -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2"
      crossorigin="anonymous"
    />

    <!-- Custom css -->
    <link rel="stylesheet" href="./css/main.css" />
  </head>
  <body>
    <!-- Form Section -->
    <section id="auth-form">
      <div class="auth--wrapper">
        <nav class="navbar navbar-expand-md px-md-5">
          <a class="navbar-brand" href="#">Navbar</a>
          <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="index.html#reservation-form"
                  >Reservations</a
                >
              </li>

              <li class="nav-item dropdown">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  id="navbarDropdown"
                  role="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  Dropdown
                </a>
                <div
                  class="dropdown-menu dropdown-menu-right"
                  aria-labelledby="navbarDropdown"
                >
                  <span class="dropdown-item"
                    >Signed in as <b>lemon potter</b></span
                  >
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="login.html">Log in</a>
                  <a class="dropdown-item" href="signup.html">Sign up</a>
                  <a class="dropdown-item" href="signup.html">Account</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li>
            </ul>
          </div>
        </nav>

        <div class="bg-image">
          <span>
            Enjoy personalized recommendations, discounts and much more
          </span>
        </div>
        <div class="form-wrapper">
          <div class="form-card">
            <h2>Log in to your account</h2>

            <h5><span class="small">Sign in with email</span></h5>
            <form action="#" method="post" class="form">
              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  name="email"
                  placeholder="Enter your email"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input
                  type="password"
                  name="password"
                  placeholder="Enter your password"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <button type="submit" name="login-user" class="btn btn-primary btn-block">
                  Log in
                </button>
              </div>

              <div class="alternate-auth">
                <span>
                  Don't have an account?
                  <a href="./signup.html">&nbsp;Sign up instead</a>
                </span>
              </div>
            </form>
            <?php if(isset($error)) { echo '<div class="alert alert-danger mt-3">' . $error . '</div>'; } ?>
            <?php if(isset($success)) { echo '<div class="alert alert-success mt-3">' . $success . '</div>'; } ?>
          </div>
        </div>
      </div>
    </section>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
      crossorigin="anonymous"
    ></script>
  </body>
</html>