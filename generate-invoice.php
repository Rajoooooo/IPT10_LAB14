<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Stripe API Key
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;
if (!$stripeSecretKey) {
    die("Stripe secret key not set in the .env file.");
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

// Fetch customers and products
try {
    $customers = \Stripe\Customer::all(['limit' => 10]); // Fetch 10 customers
    $products = \Stripe\Product::all(['active' => true]); // Fetch active products
    $prices = \Stripe\Price::all(['active' => true]);    // Fetch active prices
} catch (\Stripe\Exception\ApiErrorException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Map product prices by product ID, only including 'one_time' prices
$price_map = [];
foreach ($prices->data as $price) {
    if (isset($price->product) && $price->type === 'one_time') {
        $price_map[$price->product] = $price;
    }
}

// Handle form submission
$invoiceDetails = null; // Variable to store invoice details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'] ?? null;
    $selectedProducts = $_POST['products'] ?? [];

    // Validate input
    if (!$customerId) {
        die("Error: No customer selected.");
    }
    if (empty($selectedProducts)) {
        die("Error: No products selected.");
    }

    try {
        // Create a draft invoice
        $invoice = \Stripe\Invoice::create([
            'customer' => $customerId,
        ]);

        foreach ($selectedProducts as $priceId) {
            // Retrieve the price object
            $price = \Stripe\Price::retrieve($priceId);

            // Check if the price type is 'one_time'
            if ($price->type !== 'one_time') {
                echo "Skipping price ID {$priceId}: Not a 'one_time' price.<br>";
                continue; // Skip this price
            }

            // Create an invoice line item
            \Stripe\InvoiceItem::create([
                'customer' => $customerId,
                'price' => $priceId,
                'invoice' => $invoice->id,
            ]);
        }

        // Finalize the invoice (use instance method, not static)
        $finalizedInvoice = $invoice->finalizeInvoice();

        // Store invoice details to display
        $invoiceDetails = $finalizedInvoice;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        die("Error creating invoice: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #101010; /* Dark background for contrast */
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        h1, h2 {
            color: #fff;
        }
        .container {
            background-color: #121212;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            width: 90%;
            max-width: 800px;
        }

        .btn {
            display: inline-block;
            background-color: #e03e3e; /* Bold red */
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 20px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #d12a2a; /* Darker red */
        }

        /* Form Styles */
        select, input, button {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 18px;
            border-radius: 6px;
            border: none;
            background-color: #333;
            color: #fff;
        }

        button {
            background-color: #e03e3e;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #d12a2a;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
        }

        .product-item img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            object-fit: cover;
            margin-right: 15px;
        }

        .product-item label {
            display: flex;
            align-items: center;
            color: #ccc;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Create Invoice</h1>

        <?php if ($invoiceDetails): ?>
            <h2>Invoice Created Successfully!</h2>
            <p>Invoice ID: <?= htmlspecialchars($invoiceDetails->id) ?></p>
            <a href="<?= htmlspecialchars($invoiceDetails->invoice_pdf) ?>" target="_blank" class="btn">Download PDF</a><br>
            <a href="<?= htmlspecialchars($invoiceDetails->hosted_invoice_url) ?>" target="_blank" class="btn">Pay Invoice</a>
        <?php else: ?>
            <form action="" method="POST">
                <label for="customer">Select Customer</label>
                <select id="customer" name="customer_id" required>
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers->data as $customer): ?>
                        <option value="<?= htmlspecialchars($customer->id) ?>">
                            <?= htmlspecialchars($customer->name ?: $customer->email) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <h2>Select Products</h2>
                <?php foreach ($products->data as $product): ?>
                    <?php if (isset($price_map[$product->id])): ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($product->images[0] ?? '') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                            <div>
                                <label>
                                    <input type="checkbox" name="products[]" value="<?= htmlspecialchars($price_map[$product->id]->id) ?>">
                                    <?= htmlspecialchars($product->name) ?> - 
                                    $<?= number_format($price_map[$product->id]->unit_amount / 100, 2) ?>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <button type="submit">Generate Invoice</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
