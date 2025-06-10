<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيداع عبر PayPal</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; text-align: center; padding: 5rem; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 2rem; color: #333; }
        #paypal-button-container { margin: 2rem 0; }
        .loading { display: none; margin: 1rem auto; width: 20px; height: 20px; border: 3px solid #667eea; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id=AWJDpSZPqwZKyaZfPwouCi_WSNtIfs4TvFTwCFldWXvii1iHkcpZnVEEpZrSh6-bL-WN89GxReEGfGfM&currency=USD"></script>
</head>
<body>
    <div class="container">
        <h2>إتمام الإيداع: $<?php echo htmlspecialchars($amount); ?></h2>
        <div id="paypal-button-container"></div>
        <div id="loading" class="loading"></div>
        <p>سيتم إعادة توجيهك بعد إتمام الدفع...</p>
    </div>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($amount, 2, '.', ''); ?>',
                            currency_code: 'USD'
                        },
                        description: 'إيداع في محفظة أجيال كاش'
                    }],
                    application_context: {
                        shipping_preference: 'NO_SHIPPING'
                    }
                });
            },
            onApprove: function(data, actions) {
                document.getElementById('loading').style.display = 'block';
                return actions.order.capture().then(function(details) {
                    window.location.href = 'index.php?action=deposit_success&token=' + data.orderID;
                });
            },
            onCancel: function(data) {
                window.location.href = 'index.php?action=deposit_cancel&payment_id=<?php echo $payment_id; ?>';
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                window.location.href = 'index.php?action=deposit&error=' + encodeURIComponent('فشل تحميل الدفع! حاول مرة أخرى.');
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>