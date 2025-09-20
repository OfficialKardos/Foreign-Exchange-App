<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foreign Exchange Purchase</title>
    <?= $add_css ?>
</head>
<body>
    <div class="container">
        <h1>Foreign Exchange Purchase</h1>
        <div id="overlay" hidden></div>
        <center><img id="loader" src="<?= $loader ?>" hidden></center>
        <div class="row">
            <div class="col-md-6">
                <form id="currency-form">       
                    <div class="form-group" id="foreign_am">
                    </div>

                    <div class="form-group" id="currenciesDrop">
                    </div>

                    <div class="form-control" id="surplus_am">
                    </div>
                    
                    <div class="form-group" id="zar_am">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Purchase</button>
                </form>
            </div>
        </div>
        
        <?php if ($show_confirmation ?? false): ?>
        <div id="confirmation" class="mt-4">
            <div class="alert alert-success">
                <h4>Order Confirmation</h4>
                <p>Your order has been placed successfully!</p>
                <div id="order-details">
                    <p>Order ID: <?php echo htmlspecialchars($order_id ?? ''); ?></p>
                    <p>Currency: <?php echo htmlspecialchars($currency_code ?? ''); ?></p>
                    <p>Amount: <?php echo htmlspecialchars($foreign_amount ?? ''); ?></p>
                    <p>ZAR Paid: <?php echo htmlspecialchars($zar_amount ?? ''); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?= $add_js ?>
    
</body>
</html>