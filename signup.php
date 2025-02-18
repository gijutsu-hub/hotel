<?php
require 'vendor/autoload.php'; // Make sure AWS SDK is installed using Composer

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize DynamoDB Client
$dynamoDb = new DynamoDbClient([
    'region' => 'us-east-1', // Adjust to your AWS region
    'version' => 'latest',
    'credentials' => [
       'key'    => getenv('KEY'),
'secret' => getenv('SECRET'),

    ],
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup-user'])) {
    $phone_no = $_POST['phone_no'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Secure password hashing
    $subs = ''; // Default role

    // Prepare data to store
    $item = [
        'email'     => ['S' => $email],
        'password'  => ['S' => $password],
        'subs'      => ['S' => $subs]
    ];

    try {
        // Check if the user already exists
        $existingUser = $dynamoDb->getItem([
            'TableName' => 'AccountsTable',
            'Key' => ['email' => ['S' => $email]],
        ]);

        if (isset($existingUser['Item'])) {
            echo "<div class='alert alert-danger'>Email already registered.</div>";
        } else {
            // Add user to DynamoDB
            $dynamoDb->putItem([
                'TableName' => 'AccountsTable',
                'Item' => $item,
            ]);
            echo "<div class='alert alert-success'>Registration successful! <a href='login.html'>Login here</a>.</div>";
        }
    } catch (AwsException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <!-- Bootstrap css -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2"
      crossorigin="anonymous"
    />

    <!-- Jquery UI theme -->
    <link
      rel="stylesheet"
      href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"
    />

    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
    />

    <!-- Custom css -->
    <link rel="stylesheet" href="./css/main.css" />
  </head>
  <body>
    <!-- Form Section -->
    <section id="signup-form">
      <div class="auth--wrapper">
        <nav class="navbar navbar-expand-md px-md-5">
          <a class="navbar-brand" href="#"></a>
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
                  <span class="dropdown-item">
                    Signed in as <b>lemon potter</b>
                  </span>
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
          <div class="form-progress">
            <span>Set up your account in 3 simple steps</span>
            <div class="form-progress-item complete">Phone number</div>
            <hr />
            <div class="form-progress-item--divider d-none"></div>
            <div class="form-progress-item">Personal info</div>
            <hr />
            <div class="form-progress-item--divider d-none"></div>
            <div class="form-progress-item">Credentials</div>
          </div>
        </div>
        <div class="form-wrapper">
          <div class="form-card">
            <h2>Sign up</h2>
            <form action="" method="post" class="form">
              <!-- Part 1 of form -->
              <div class="part part-1">
                <div class="form-group">
                  <label for="phone-no">Phone number</label>
                  <input
                    type="tel"
                    name="phone-no"
                    class="form-control"
                    placeholder="Enter your phone number"
                  />
                </div>

                <!-- Step 1 buttons -->
                <div class="form-group">
                  <div class="step">
                    <a class="btn" href="./index.html" id="cancel-part-1"
                      >Cancel</a
                    >
                    <button class="btn" id="proceed-part-2">Next</button>
                  </div>
                </div>
                <!-- Step 1 buttons end -->
              </div>

              <!-- Part 2 of form -->
              <div class="part part-2">
                <div class="form-group">
                  <label for="fname">First name</label>
                  <input
                    type="text"
                    class="form-control"
                    name="fname"
                    placeholder="Enter your first name"
                  />
                </div>
                <div class="form-group">
                  <label for="lname">Last name</label>
                  <input
                    type="text"
                    class="form-control"
                    name="lname"
                    placeholder="Enter your last name"
                  />
                </div>
                <div class="form-group">
                  <label for="dob">Date of birth</label>
                  <input
                    type="text"
                    class="form-control"
                    name="dob"
                    id="dob"
                    placeholder="Enter your birth date"
                  />
                </div>

                <!-- Step 2 buttons -->
                <div class="form-group">
                  <div class="step">
                    <button class="btn" id="cancel-part-2">Back</button>
                    <button class="btn" id="proceed-part-3">Next</button>
                  </div>
                </div>
                <!-- Step 2 buttons end -->
              </div>

              <!-- Part 3 of form -->
              <div class="part part-3">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    class="form-control"
                  />
                </div>

                <!-- Step 3 buttons -->
                <div class="form-group">
                  <div class="step">
                    <button class="btn" id="cancel-part-3">Back</button>
                    <button
                      type="submit"
                      name="signup-user"
                      class="btn btn-primary"
                    >
                      Signup
                    </button>
                  </div>
                </div>
                <!-- Step 3 buttons end -->
              </div>
              <!-- Part 3 of form ends -->

              <div class="alternate-auth">
                <span>
                  Already have an account?
                  <a href="./login.html">&nbsp;Login here</a>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Jquery -->
    <script
      src="https://code.jquery.com/jquery-3.5.1.min.js"
      integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
      crossorigin="anonymous"
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Bootstrap  -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
      crossorigin="anonymous"
    ></script>

    <script src="./js/handle-sign-up.js"></script>

    <!-- Custom -->
    <script>
      $(document).ready(function () {
        var d = new Date();
        var year = d.getFullYear();
        d.setFullYear(year);
        $("#dob").datepicker({
          changeMonth: true,
          changeYear: true,
          yearRange: "1930:" + year + "",
          defaultDate: d,
        });
        $("nav").addClass("navbar-light");
      });
    </script>
  </body>
</html>
