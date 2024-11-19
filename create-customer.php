<?php
require "init.php"; // Ensure Stripe SDK is properly initialized

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $line1 = $_POST['line1'];
    $line2 = $_POST['line2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $postal_code = $_POST['postal_code'];

    try {
        // Create customer in Stripe
        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $line1,
                'line2' => $line2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'postal_code' => $postal_code
            ]
        ]);
        echo "<script>alert('Customer successfully registered!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error: Unable to register customer. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(120deg, #111, #333);
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
            backdrop-filter: blur(10px);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff;
        }
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #ccc;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid #555;
            color: white;
            font-size: 16px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-control:focus {
            outline: none;
            border-color: #f7a300;
            background-color: rgba(255, 255, 255, 0.3);
        }
        .btn-dark {
            background-color: #f7a300;
            color: #333;
            border-radius: 8px;
            font-size: 16px;
            padding: 15px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-dark:hover {
            background-color: #f77f00;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            font-size: 14px;
            color: #ccc;
        }
        .row .col-md-6 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form class="form-container" method="POST" action="">
        <h2>Create Your Account</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Complete Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="line1" class="form-label">Address Line 1</label>
                    <input type="text" id="line1" name="line1" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="line2" class="form-label">Address Line 2</label>
                    <input type="text" id="line2" name="line2" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" name="state" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" name="country" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" required>
                </div>
            </div>
        </div>
        <button type="submit" class="btn-dark w-100">Register</button>
    </form>
    <div class="footer">
        <p>By registering, you agree to our <a href="#" style="color: #f7a300;">Terms & Conditions</a>.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
