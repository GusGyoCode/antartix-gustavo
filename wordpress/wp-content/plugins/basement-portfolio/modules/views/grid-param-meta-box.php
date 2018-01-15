<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
    return;
}
?>

<div class="basement_html_param-block" id="<?php echo BASEMENT_PORTFOLIO_TEXTDOMAIN . '_' . $post->ID; ?>">
    <div class="basement_html_param-body">
        <?php
        if( $params ) {
            $j = 0;
            foreach ( $params as $key_setting => $setting ) {

                foreach($setting['blocks'] as $key_block => $block) { ?>
                    <?php if(!empty($block['title'])) { ?>
                        <h2><?php echo $block['title']; ?></h2>
                        <hr>
                    <?php } ?>

                    <?php if(!empty($block['title'])) { ?>
                        <div class="basement_html_param-wrap-setting">
                    <?php } ?>

                    <?php foreach($block['params'] as $key_param => $param) {
                        if($key_param === 'header_position') { ?>
                            <div class="basement_html_param-setting cf" id="<?php echo $key_param; ?>">
                                <?php
                                foreach ($param as $deep_key => $deep_value) { ?>
                                    <div class="basement_html_param-info-setting">
                                        <h3><?php echo $deep_value['title']; ?></h3>
                                        <i><?php echo $deep_value['description']; ?></i>
                                    </div>
                                    <div class="basement_html_param-action-setting">
                                        <?php echo $deep_value['input']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="basement_html_param-setting cf">
                                <div class="basement_html_param-info-setting">
                                    <h3><?php echo $param['title']; ?></h3>
                                    <i><?php echo $param['description']; ?></i>
                                </div>
                                <div class="basement_html_param-action-setting">
                                    <?php echo $param['input']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <?php if(!empty($block['title'])) { ?>
                        </div>
                     <?php } ?>


                <?php }

                ?>

            <?php }
        } ?>
    </div>
</div>

