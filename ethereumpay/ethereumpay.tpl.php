<?php 
/*
available variable

$order_id
*/
?>
<?php if(false): ?>
<script type="text/javascript" src="<?php echo C9WEP_URL . 'assets/lib/ethers-5.0.umd.min.js'; ?>" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo C9WEP_URL . 'assets/bootstrap-4.1.3/css/bootstrap.css'; ?>">
<script type="text/javascript" src="<?php echo C9WEP_URL . 'assets/lib/jquery.3.2.1.min.js'; ?>"></script>
<?php endif;//end false ?>

<script type="text/javascript" src="<?php echo C9WEP_URL . 'assets/lib/jquery/jquery.ba-throttle-debounce.min.js'; ?>"></script>
<script src="https://cdn.ethers.io/lib/ethers-5.0.umd.min.js" type="application/javascript"></script>
<script type="text/javascript" src="<?php echo C9WEP_URL . 'assets/lib/qrcode.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo C9WEP_URL . 'assets/bootstrap-4.1.3/css/bootstrap-grid.css'; ?>">
<?php 
  include C9WEP_DIR . '/admin/ajax/ft_check_transaction_status/ft_check_transaction_status.js.php';
?>
<?php 
// PAYMENT_MODE,
// ETHER_NETWORK,
// ETHER_AMOUNT,
// ETHER_WEI_AMOUNT,
// PAYMENT_EXPIRED_TIME,
// ETHER_TRACK_ID,
// ETHER_MY_WALLET_ADDRESS,
// ETHER_EXCHANGE_RATE,
$metas=c9wep_get_order_metas($order_id);
// ob_start();
// print_r($metas);
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// $data1=ob_get_clean();
// file_put_contents(dirname(__FILE__)  . '/metas.log',$data1,FILE_APPEND);
$ether_amount=$metas[ETHER_AMOUNT];
$is_test_mode=c9wep_get_payment_mode_by_order_id($order_id);
$wallet_address=$metas[ETHER_MY_WALLET_ADDRESS];//c9wep_get_wallet_address_with_order_id($order_id);
$is_payment_expired=c9wep_is_payment_expired($order_id);

// $ether_amount=c9wep_get_order_amount_ether($order_id);
// $is_test_mode=c9wep_get_payment_mode_by_order_id($order_id);
// $wallet_address=c9wep_get_wallet_address_with_order_id($order_id);
// $is_payment_expired=c9wep_is_payment_expired($order_id);
// ob_start();
// print_r($is_payment_expired);
// echo PHP_EOL;
// $data1=ob_get_clean();
// file_put_contents(dirname(__FILE__)  . '/is_payment_expired.log',$data1,FILE_APPEND);
?>
<div class="bst4-wrapper ethereumpay-wrapper">
    <div class="container-fluid">
      <?php if($is_test_mode): ?>
      <div class="row payment-test-mode-row-wrapper">
          <div class="col col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 test-mode-notice-col-wrapper">
              <div class="test-mode-notice-inner">
                  This is Test Mode
              </div> <!-- test-mode-notice-inner -->
          </div> <!-- test-mode-notice-col-wrapper-->
      </div> <!-- row payment-test-mode-row-wrapper-->
      
      <style>
        .test-mode-notice-inner{
          color: #fff;
          background: red;
          padding: 5px;
          margin: 5px 0 5px 0;
          text-transform: uppercase;
        } /*.test-mode-notice-inner*/
      </style>
      <?php endif;//end false ?>
      <div class="row ethereumpay-row-wrapper">
          <div class="col col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ethereumpay-col-wrapper">
              <div class="ethereumpay-inner">
                <?php if($is_test_mode): ?>
                <?php 
                    $network=c9wep_get_ether_network_by_order_id($order_id);
                    $network_txt=c9wep_get_test_networks_by_order_id($order_id);
                    $wallet_link=c9wep_get_wallet_address_transaction_view_link($network, $wallet_address,'View Wallet');
                ?>
                  <div class="check-wallet-in-test-work">
                    Ether Network: <b><?php echo $network_txt; ?></b>
                    <?php echo $wallet_link; ?>
                  </div>
                <?php endif;//end $is_test_mode ?>
                <div class="ether-amount">
                  Amount: <span class="amount txt-bold bg-gray pd-small mg-small"><?php echo $ether_amount; ?></span>ETH
                </div>
                <div class="ether-to-address">
                  Send To: <span class="to-address txt-bold bg-gray pd-small mg-small"><?php echo $wallet_address; ?></span>
                </div>
              </div> <!-- ethereumpay-inner -->
          </div> <!-- ethereumpay-col-wrapper-->
      </div> <!-- row ethereumpay-row-wrapper-->

      <div class="row ether-pay-qrcode-button-row-wrapper">
          <div class="col col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 ether-pay-qrcode-col-wrapper">
              <div class="ether-pay-qrcode-inner">
                <div id="qrcode-wrapper">
                  <div id="ethereumpay-qrcode" class="ethereumpay-qrcode">
                      
                  </div> <!-- ethereumpay-qrcode-inner -->
                </div>

            <?php if($is_test_mode): ?>
                Or 
                <button href="javascript:void(0);" id="pay-ether" class="button button-default btn btn-primary">Pay Via MetaMask</button>
            <?php endif;//end false ?>
              </div> <!-- ether-pay-qrcode-inner -->

          </div> <!-- ether-pay-qrcode-col-wrapper-->
          <style type="text/css">
            .ethereumpay-wrapper{
              margin: 0 0 0 2rem;
            }
            .ether-pay-qrcode-inner{
              display: flex;
              justify-content: start;
              align-items: center;
            }

            #qrcode-wrapper{
              padding: 8px;
              border: 1px solid #aaa;
              margin: 5px;
            }

            .mg-small{
              /*margin: 5px;*/
            }

            #pay-ether{
              border-radius: 5px;
              margin-left: 10px;
            }
          </style>
      </div> <!-- row ether-pay-qrcode-button-row-wrapper-->

      <?php 
        do_action( 'c9wep_pay_for_order_bottom', $order_id);
      ?>

      <?php 
      include __DIR__ . '/timeleft-coutdown.tpl.php';
      include __DIR__ . '/transaction-status-panel.tpl.php';
      ?>
    </div>
</div>
<?php 
//$qr_code_url=sprintf('ethereum:%s?value=%s', $wallet_address , $ether_amount);
?>
<style>
  .txt-bold{
    font-weight: bold;
  }
  .bg-gray{
    background: #eee;
  }
  .pd-small{
    padding: 2px 5px;
  }
  .ethereumpay-inner{

  } /*.ethereumpay-inner*/
</style>
<script type="text/javascript">
      jQuery(function($){
        const ethereum_root='ethereum:'+'<?php echo $wallet_address; ?>'+'?';
        const wei=ethers.utils.parseEther("<?php echo $ether_amount; ?>");
        const value='value=' + wei;
        // const data='&data=' + ethers.utils.hexlify('0x' + wei + '<?php echo $order_id; ?>');
        // ethers.utils.hexlify(5)
        // const nonce='&nonce=' + '<?php echo "test_" . $order_id; ?>';
        const qr_code_url=ethereum_root + value;
        // console.log('qr_code_url:');
        // console.log(qr_code_url);

        const qrCode = new QRCode('ethereumpay-qrcode', {
          // text: '<?php echo $qr_code_url; ?>',
          text: qr_code_url,
          // width: 128,
          // height: 128,
          width: 192,
          height: 192,
          // colorDark: '#424770',
          // colorLight: '#f8fbfd',
          correctLevel: QRCode.CorrectLevel.H,
        });

        $('#pay-ether').click(() => {
          if (typeof window.ethereum !== 'undefined' || (typeof window.web3 !== 'undefined')) {
            // Web3 browser user detected. You can now use the provider.
            // const accounts = await window.ethereum.enable();
            // const curProvider = window['ethereum'] || window.web3.currentProvider

            const provider = new ethers.providers.Web3Provider(window.ethereum);

            // console.log('accounts: ', accounts);
            console.log('provider: ', provider);

            const signer = provider.getSigner();

            // Send 1 ether to an ens name.
            // const tx = signer.sendTransaction({
            //     to: "<?php echo $wallet_address; ?>",
            //     value: ethers.utils.parseEther("<?php echo $ether_amount; ?>")
            // });   

            const tx = {
                to: "<?php echo $wallet_address; ?>",
                value: ethers.utils.parseEther("<?php echo $ether_amount; ?>")
            }

            // const result = await signer.sendTransaction(tx)
            signer.sendTransaction(tx)
                  .then(result => {
                      console.log('then -> ', result);
                      /*
                      $("#ether_transaction_status").val('init');
                      $("#ether_transaction_init").val(JSON.stringify(result));
                      // Get notified when a transaction is mined\
                      let transactionHash=result.hash;
                      provider.once(transactionHash, function(transaction) {
                          console.log('Transaction Minded: ' + transaction.hash);
                          console.log(transaction);
                        $("#ether_transaction_status").val('success');
                        $("#ether_transaction_confirm").val(JSON.stringify(transaction));
                      });
                      */
                  })
                  .catch(err => {
                    /*
                      $("#ether_transaction_status").val('failed');
                      $("#ether_transaction_init").val(JSON.stringify(err));
                      */
                      console.log(err);
                  });
            // console.log('result:');
            // console.log(result);
          }
        }); 
      });
</script>
