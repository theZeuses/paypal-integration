<script src="https://www.paypal.com/sdk/js?&client-id={{ env('PAYPAL_CLIENT_ID') }}"></script>
<div id="smart-button-container">
    <div style="text-align: center;">
    <div id="paypal-button-container"></div>
    </div>
</div>
    
    <script>
      paypal.Buttons({
        style: {
            shape: 'pill',
            color: 'gold',
            layout: 'horizontal',
            label: 'paypal',
            
        },
        createOrder: function (data, actions) {
          return fetch('/api/create-order', {
            method: 'POST'
          }).then(function(res) {
            return res.json();
          }).then(function(data) {
            return data.id;
          });
        },
        onApprove: function (data, actions) {
          return fetch('/api/capture-order/' + data.orderID, {
            method: 'POST'
          }).then(function(res) {
            if (!res.ok) {
              alert('Something went wrong');
            }
          });
        }
      }).render('#paypal-button-container');
    </script>

