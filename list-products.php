<?php

require "init.php";

// Fetch all products
try {
    $products = $stripe->products->all();
} catch (Exception $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store - Product List</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(120deg, #f3f4f6, #e9ecef);
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background: #1e1e1e;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .container {
            max-width: 1300px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .product-details {
            padding: 20px;
        }
        .product-card h3 {
            font-size: 20px;
            margin: 10px 0;
            color: #1e1e1e;
        }
        .product-card p {
            margin: 8px 0;
            font-size: 14px;
            color: #666;
        }
        .product-card .price {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-top: 10px;
        }
        .product-card button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            margin: 20px;
            font-size: 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .product-card button:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #1e1e1e;
            color: #fff;
            margin-top: 40px;
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <header>
        Welcome to Our Store
    </header>
    <div class="container">
        <div class="product-container">
            <?php
            foreach ($products->data as $product) {
                try {
                    $price = $stripe->prices->retrieve($product->default_price);
                    $price_amount = number_format($price->unit_amount / 100, 2);
                    $currency = strtoupper($price->currency);
                } catch (Exception $e) {
                    $price_amount = "N/A";
                    $currency = "";
                }

                $name = htmlspecialchars($product->name);
                $image = array_pop($product->images) ?: 'https://via.placeholder.com/300';

                echo "
                    <div class='product-card'>
                        <img src='" . htmlspecialchars($image) . "' alt='Product Image'>
                        <div class='product-details'>
                            <h3>$name</h3>
                            <p class='price'>$currency $price_amount</p>
                        </div>
                        <button>Add to Cart</button>
                    </div>
                ";
            }
            ?>
        </div>
    </div>
    <footer>
        © 2024 Modern Store. All Rights Reserved by <span>Rafael John L. Castro</span>.
    </footer>
</body>
</html>
