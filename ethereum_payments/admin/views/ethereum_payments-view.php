<?php 
function c9wep_row($key,$value){
    ob_start();
    ?>
    <tr class="row-<?php echo $key; ?>">
        <th scope="row" style="text-align: right">
            <label for="<?php echo $key; ?>"><?php echo $key; ?></label>
        </th>
        <td style="padding-left: 10px;">
            <?php 
                $data=unserialize($value); 
            ?>
            <?php if(is_array($data) || is_object($data)): ?>
                <?php 
                    echo '<pre>';
                    echo print_r($data);
                    echo '</pre>';
                ?>
            <?php else: ?>
                <?php echo $value; ?>
            <?php endif;//end is_array($data) || is_object($data) ?>
        </td>
    </tr>
    <?php
    $html=ob_get_clean();
    return $html;
}
?>
<div class="wrap">
    <h2><?php _e( 'Refer', 'c9s' ); ?></h2>

    <?php $item = c9wep_get_ethereum_payments_by_id( $id ); ?>
    <?php unset($item->id) ?>
    <table>
        <tbody>
            <?php foreach ($item as $key => $value): ?>
                <?php echo c9wep_row($key , $value); ?>
            <?php endforeach ?>
         </tbody>
    </table>
</div>