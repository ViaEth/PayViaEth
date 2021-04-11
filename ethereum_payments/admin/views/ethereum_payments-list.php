<div class="wrap">
    <?php 
        $list_table = new C9wep_Ethereum_payments_List();
    ?>
    <h2><?php _e( 'Ethereum Payments', 'c9wep' ); ?></h2>
    <?php if (array_key_exists('error', $_GET)): ?>
        <div class="notice notice-error"><p><?php echo $_GET['error']; ?></p></div>
    <?php endif; ?>
    <?php if (array_key_exists('success', $_GET)): ?>
        <div class="notice notice-success"><p><?php echo $_GET['success']; ?></p></div>
    <?php endif; ?>
    <?php 
        // if(empty($_GET['year'])){
        //     $_GET['year']=date("Y");
        // }
        // if(empty($_GET['month'])){
        //     $_GET['month']=date('m');
        // }
        // if(empty($_GET['shipping_status'])){
        //     $_GET['shipping_status']='processing';
        // }
   ?>
    <?php //echo $list_table->get_views(); ?>
    <form method="post">
        <input type="hidden" name="page" value="ttest_list_table">

        <?php
        $list_table->prepare_items();
        $list_table->search_box( 'search', 's' );
        $list_table->display();
        ?>
    </form>
</div>
<style type="text/css">
    .links-title{
        margin: 0px !important;
    }
    .subsubsub{
        margin: 0px 0px 8px 0 !important;
        float: none;
    }
</style>