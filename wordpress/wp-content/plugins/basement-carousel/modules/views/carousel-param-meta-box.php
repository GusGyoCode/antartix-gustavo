<?php
defined('ABSPATH') or die();

if( empty($params) ) {
    return;
}

?>
<div class="z_tabs-area cf">

    <div class="z_tabs-nav">
        <ul>
            <?php
            if( $params ) {
                $i = 0;
                foreach ( $params as $key_param => $param ) {
                    ?>
                    <li <?php echo $param['active'] ? 'class="active"' : '';  ?>>
                        <a href="#<?php echo $key_param; ?>" title=""><i class="fa <?php echo $param['fa']; ?>"></i><span><?php echo $param['title']; ?></span></a>
                        <div class="z_tabs-tooltip">
                            <div class="z_tooltip-body"><?php echo $param['short_title']; ?></div>
                        </div>
                    </li>
                <?php }
            }?>
        </ul>
    </div>


    <div class="z_tabs-content">


        <?php
        if( $params ) {

            $j = 0;
            foreach ( $params as $key_param1 => $param1 ) {
                ?>
                <div class="z_tab <?php echo $param1['active'] ? 'active' : ''; ?>" id="<?php echo $key_param1; ?>">

                    <?php

                    foreach($param1['blocks'] as $key_block => $block) { ?>
                        <div class="z_panel-setting cf">
                            <div class="z_info-setting">
                                <h3><?php echo !empty($block['title']) ? $block['title'] : ''; ?></h3>
                                <i><?php echo !empty($block['description']) ? $block['description'] : '' ; ?></i>
                            </div>

                            <div class="z_action-setting">
                                <?php echo !empty($block['input']) ? $block['input'] : ''; ?>
                            </div>
                        </div>

                    <?php } ?>


                </div>
            <?php }
        } ?>


    </div>
</div>
