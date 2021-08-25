<?php

namespace WP2StaticCopy;

use WP_CLI;


/**
 * WP2StaticS3 WP-CLI commands
 *
 * Registers WP-CLI commands for WP2StaticCopy under main wp2static cmd
 *
 * Usage: wp wp2static options set copy folder
 */
class CLI {

    /**
     * S3 commands
     *
     * @param string[] $args CLI args
     * @param string[] $assoc_args CLI args
     */
    public static function copy(
        array $args,
        array $assoc_args
    ) : void {
        $action = isset( $args[0] ) ? $args[0] : null;

        if ( empty( $action ) ) {
            WP_CLI::error( 'Missing required argument: <options>' );
        }

        if ( $action === 'options' ) {
            WP_CLI::line( 'TBC setting options for Copy addon' );
        }
    }
}

