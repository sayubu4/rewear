
document.querySelectorAll('.buy-button').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const productName = this.dataset.productName;
        const productPrice = this.dataset.productPrice;
        
        fetch('cart_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: action=add&product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_price=${productPrice}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Added to cart!');
                // Optional: Update cart count in navigation if you have one
            }
        });
    });
});
