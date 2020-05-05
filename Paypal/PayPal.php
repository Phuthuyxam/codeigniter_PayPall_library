<?php 
    /* 
        author : phuthuyxam 
        add paypal to CI
    */
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class PayPal{
        
        // config sandboxMode = on or sandboxMode = off
        protected $config = array (
            'client_ID' => '',
            'sandbox_clienID' => 'ATNqhSrvoIQ109TLGxl1sTmSY23jteQpuD5l_u7HGbKaOSNjkdpCqPt7iGkmeNkj1ysrb3RyxEFYD09D',
            'sandboxMode' => 'on',
            'processProcess' => 'default_route'
        );
 
        public function __construct($params)
        {
            $err = array();

            if( !isset($params['amount']) || empty($params['amount']) ){
                $err['amount'] = "amount is require";
            }else{
                !is_numeric($params['amount']) ? $err['amount'] = "Amount is numberic" : false ;
            }

            echo ( empty($err) ? $this->init($params['amount']) : json_encode($err) ); 
        }
        

        public function init($amount){
            
            $client_id = $this->config['sandboxMode'] == 'on' ? $this->config['sandbox_clienID'] : $this->config['client_ID'];
            // load sdk paypal
            ?>
                <style>
                    /* Media query for mobile viewport */
                    @media screen and (max-width: 400px) {
                        #paypal-button-container {
                            width: 100%;
                        }
                    }
                    /* Media query for desktop viewport */
                    @media screen and (min-width: 400px) {
                        #paypal-button-container {
                            width: 250px;
                        }
                    }
                </style>
                <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $client_id ?>"> </script>

                <div id="paypal-button"></div>

                <script> 
                    let amount = "<?php echo $amount ?>"
                    paypal.Buttons({
                        style: {
                            size: 'small',
                            color: 'gold',
                            shape: 'pill',
                            label: 'credit',
                        },
                        createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                            amount: {
                                value: amount
                            }
                            }]
                        });
                        },
                        onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            alert('Transaction completed by ' + details.payer.name.given_name);
                            // Call your server a router after success
                            console.log(details);
                            return fetch('<?php echo "/".$this->config['processProcess'] ?>', {
                                method: 'post',
                                headers: {
                                    'content-type': 'application/json'
                                },
                                body: JSON.stringify({
                                    dataPayPal: details
                                })
                            });
 
                        });
                        },
                        onError: function(error){
                            alert("Payment failed !");
                            // console.log(error);
                        }
                    }).render('#paypal-button');
                </script>
            <?php
        }

    }
?>


