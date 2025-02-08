<?php
require 'vendor/autoload.php'; // Ensure AWS SDK is installed via Composer
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: /');
    exit();
}

$email = $_SESSION['email'] ?? "";
$subs = $_SESSION['subs'] ?? "";

// Initialize DynamoDB Client
$dynamoDb = new DynamoDbClient([
    'region' => 'us-east-1', // Update with your AWS region
    'version' => 'latest',
    'credentials' => [
        'key'    => getenv('KEY'),
'secret' => getenv('SECRET'),

    ],
]);

try {
    // Fetch subscription details from SubscriptionTable
    $result = $dynamoDb->getItem([
        'TableName' => 'SubscriptionTable', // Replace with your DynamoDB subscription table name
        'Key' => [
            'email' => ['S' => $email] // Using email as the partition key
        ]
    ]);

    if (isset($result['Item'])) {
        // Subscription exists, extract details
        $subscriptionType = $result['Item']['subscription_type']['S'] ?? 'N/A';
        $expiryDate = $result['Item']['expiry_date']['S'] ?? 'N/A';
    } else {
        // No subscription found
        $subscriptionType = 'No Subscription';
        $expiryDate = 'N/A';
    }

} catch (AwsException $e) {
    // Handle DynamoDB errors
    $subscriptionType = 'Error fetching subscription';
    $expiryDate = 'N/A';
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accounts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        body {
            background-image: url(https://mobirise.com/extensions/hotelm4/assets/images/background13-1920x1280.jpg);
            background-size: cover;
        }

        body::before {
            content: "";
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            width: 100%;
            height: 100% !important;
            background: rgba(47, 103, 177, 0.6);
        }

        .container {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }

        .Account-page {
            padding: 30px;
            width: 600px;
            border: black;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.15);
            background-color: whitesmoke;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-family: "Sofia";
            color: white;
        }

        .form-control {
            border-radius: 20px !important;
        }

        .sidenav {
            width: 100%;
            background-color: whitesmoke;
            padding: 10px;
            border-radius: 10px;
        }

        .sidenav a {
            padding: 6px 8px;
            text-decoration: none;
            font-size: 15px;
            color: #818181;
            display: block;
        }

        .sidenav a:hover {
            color: lightblue;
        }

        .avatar-pic {
            width: 50px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md px-md-5">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.html#rooms">Rooms & Suites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#reservation-form">Reservations</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Dropdown</a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <span class="dropdown-item">Signed in as <b><?php echo htmlspecialchars($email); ?></b></span>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="login.html">Log in</a>
                            <a class="dropdown-item" href="signup.html">Sign up</a>
                            <a class="dropdown-item" href="accounts.html">Account</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <h1>Request Booking</h1>
    <div class="container">
        <div class="row">
            <div class="col-4">
                <div class="sidenav">
                    <a href="accounts.html" style="background-color: mediumturquoise">Settings</a>
                    <hr />
                    <a href="#">Account</a>
                    <hr />
                    <a href="#Notifications">Notifications</a>
                    <hr />
                    <a href="#password">Password</a>
                    <hr />
                </div>
            </div>
            <div class="col-8">
                <div class="Account-page">
                    <h3>Subscription Details</h3>
                    <p><strong>Subscription Type:</strong> <?php echo htmlspecialchars($subscriptionType); ?></p>
                    <p><strong>Expiry Date:</strong> <?php echo htmlspecialchars($expiryDate); ?></p>

                    <?php if (!empty($_SESSION['subs'])): ?>
                        <form action="upload.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="fileUpload">Upload your file:</label>
                                <input type="file" name="file" id="fileUpload" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    <?php else: ?>
                        <p style="color: red;">Subscription required to upload files.</p>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <p style="color: red;">Error: <?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
