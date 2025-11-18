<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<button id="pay-btn">Pay</button>

<script>
    var options = {
        "key": "{{ $key }}",
        "amount": "{{ $amount }}",
        "currency": "INR",
        "name": "Your Company",
        "description": "Test Transaction",
        "order_id": "{{ $order_id }}",
        "handler": function(response) {
            console.log(response);
            
            // Handle post-payment logic here
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('pay-btn').onclick = function(e) {
        rzp1.open();
        e.preventDefault();
    }
</script>
