<?php
/**
 * The Deactivate class
 *
 * @since      x.x.x
 *
 * @package    FlexProduct
 */
namespace Inc\Base;

class Deactivate {
    /**
     * deactivate function
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}