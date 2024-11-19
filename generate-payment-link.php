<?php
require "init.php";

$products = $stripe->products->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_products = $_POST['products'];

    try {
        $line_items = [];
        foreach ($selected_products as $product_id) {
            $price = $stripe->prices->retrieve($product_id);
            $line_items[] = [
                'price' => $price->id,
                'quantity' => 1,
            ];
        }

        $payment_link = $stripe->paymentLinks->create([
            'line_items' => $line_items,
        ]);

        header("Location: " . $payment_link->url);
        exit;
    } catch (Exception $e) {
        echo "Error creating payment link: " . $e->getMessage();
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payment Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-weight: bold;
            font-size: 1.8rem;
            color: #111;
            text-align: center;
        }
        .form-check-input {
            accent-color: #000;
        }
        .form-check-label {
            font-size: 1rem;
            color: #444;
        }
        .btn-primary {
            background-color: #111;
            border: none;
            font-weight: bold;
            letter-spacing: 0.5px;
            padding: 0.6rem 1.2rem;
            text-transform: uppercase;
        }
        .btn-primary:hover {
            background-color: #333;
        }
        footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1>Generate Payment Link</h1>
    <form method="POST" class="row g-3">
        <div class="col-md-12">
            <label class="form-label mb-3">Select Products</label>
            <?php foreach ($products->data as $product): ?>
                <div class="form-check">
                    <input type="checkbox" name="products[]" value="<?= $product->default_price ?>" class="form-check-input" id="product-<?= $product->id ?>">
                    <label class="form-check-label" for="product-<?= $product->id ?>">
                        <?= $product->name ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Generate Payment Link</button>
        </div>
    </form>
    <footer>
        Powered by <strong>Shoe Zone</strong> Style | Your Trusted Brand
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>
