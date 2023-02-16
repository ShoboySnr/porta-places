<?php
/**
 * Add office link buttons via `cvgt_office_btn_group` shortcode.
 */
?>
<div class="office-buttons">
<?php
    foreach ( $offices as $office ):
        $title      = get_the_title( $office->ID );
        $office_id  = strval( $office->ID );
?>
        <a href="#<?php echo esc_attr( $office_id ) ?>"><?php echo esc_attr( $title ); ?></a>
<?php endforeach; ?>
</div>