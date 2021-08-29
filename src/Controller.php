<?php

namespace WP2StaticDirectoryDeployer;

class Controller {
    public function run() : void {
        add_filter(
            'wp2static_add_menu_items',
            [ 'WP2StaticDirectoryDeployer\Controller', 'addSubmenuPage' ]
        );

        add_action(
            'admin_post_wp2static_directory_deployment_save_options',
            [ $this, 'saveOptionsFromUI' ],
            15,
            1
        );

        add_action(
            'wp2static_deploy',
            [ $this, 'deploy' ],
            15,
            2
        );

        add_action(
            'admin_menu',
            [ $this, 'addOptionsPage' ],
            15,
            1
        );

        do_action(
            'wp2static_register_addon',
            'wp2static-addon-directory-deployment',
            'deploy',
            'Directory Deployment',
            'https://github.com/twardoch/wp2static-addon-directory-deployment',
            'Deploys to local directory, either overwriting or replacing existing files'
        );

        if ( defined( 'WP_CLI' ) ) {
            \WP_CLI::add_command(
                'wp2static directory-deployment',
                [ CLI::class, 'directoryDeployment' ]
            );
        }
    }

    /**
     *  Get all add-on options
     *
     *  @return mixed[] All options
     */
    public static function getOptions() : array {
        global $wpdb;
        $options = [];

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $rows = $wpdb->get_results( "SELECT * FROM $table_name" );

        foreach ( $rows as $row ) {
            $options[ $row->name ] = $row;
        }

        return $options;
    }

    /**
     * Seed options
     */
    public static function seedOptions() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $query_string =
            "INSERT IGNORE INTO $table_name (name, value, label, description) " .
            'VALUES (%s, %s, %s, %s);';

        $query = $wpdb->prepare(
            $query_string,
            'directoryDeploymentDeleteBeforeDeployment',
            '1',
            'Delete target folder before deployment',
            ''
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'directoryDeploymentTargetDirectory',
            '',
            'Target directory (absolute path)',
            ''
        );

        $wpdb->query( $query );

        $query = $wpdb->prepare(
            $query_string,
            'directoryDeploymentAdditionalSourceDirectory',
            '',
            'Additional source directory to include in deployment (absolute path)',
            ''
        );

        $wpdb->query( $query );
    }

    /**
     * Save options
     *
     * @param mixed $value option value to save
     */
    public static function saveOption( string $name, $value ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $wpdb->update(
            $table_name,
            [ 'value' => $value ],
            [ 'name' => $name ]
        );
    }

    public static function renderDirectoryDeployerPage() : void {
        self::createOptionsTable();
        self::seedOptions();

        $view = [];
        $view['nonce_action'] = 'wp2static-directory-deployment-options';
        $view['uploads_path'] = \WP2Static\SiteInfo::getPath( 'uploads' );
        $directory_deployment_target =
            \WP2Static\SiteInfo::getPath( 'uploads' ) . 'wp2static-processed-site.copy';

        // TODO: why do we need this here?
        $view['options'] = self::getOptions();

        $view['copy_url'] =
            is_file( $directory_deployment_target ) ?
                \WP2Static\SiteInfo::getUrl( 'uploads' ) . 'wp2static-processed-site.copy' : '#';

        require_once __DIR__ . '/../views/directory-deployment-page.php';
    }


    public function deploy( string $processed_site_path, string $enabled_deployer ) : void {
        if ( $enabled_deployer !== 'wp2static-addon-directory-deployment' ) {
            return;
        }

        \WP2Static\WsLog::l( 'Directory deployment Addon deploying' );

        $directory_deployer = new Deployer();
        $directory_deployer->uploadFiles( $processed_site_path );
    }

    public static function createOptionsTable() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            value VARCHAR(255) NOT NULL,
            label VARCHAR(255) NULL,
            description VARCHAR(255) NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // dbDelta doesn't handle unique indexes well.
        $indexes = $wpdb->query( "SHOW INDEX FROM $table_name WHERE key_name = 'name'" );
        if ( 0 === $indexes ) {
            $result = $wpdb->query( "CREATE UNIQUE INDEX name ON $table_name (name)" );
            if ( false === $result ) {
                \WP2Static\WsLog::l( "Failed to create 'name' index on $table_name." );
            }
        }
    }

    public static function activateForSingleSite(): void {
        self::createOptionsTable();
        self::seedOptions();
    }

    public static function deactivateForSingleSite() : void {
    }

    public static function deactivate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::deactivateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::deactivateForSingleSite();
        }
    }

    public static function activate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::activateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::activateForSingleSite();
        }
    }

    /**
     * Add WP2Static submenu
     *
     * @param mixed[] $submenu_pages array of submenu pages
     * @return mixed[] array of submenu pages
     */
    public static function addSubmenuPage( array $submenu_pages ) : array {
        $submenu_pages['directorydeployer'] = [ 'WP2StaticDirectoryDeployer\Controller', 'renderDirectoryDeployerPage' ];

        return $submenu_pages;
    }

    public static function saveOptionsFromUI() : void {
        check_admin_referer( 'wp2static-directory-deployment-options' );

        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['directoryDeploymentDeleteBeforeDeployment'] ) ],
            [ 'name' => 'directoryDeploymentDeleteBeforeDeployment' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['directoryDeploymentTargetDirectory'] ) ],
            [ 'name' => 'directoryDeploymentTargetDirectory' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['directoryDeploymentAdditionalSourceDirectory'] ) ],
            [ 'name' => 'directoryDeploymentAdditionalSourceDirectory' ]
        );

        wp_safe_redirect( admin_url( 'admin.php?page=wp2static-addon-directory-deployment' ) );
        exit;
    }

    /**
     * Get option value
     *
     * @return string option value
     */
    public static function getValue( string $name ) : string {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_directory_deployment_options';

        $sql = $wpdb->prepare(
            "SELECT value FROM $table_name WHERE" . ' name = %s LIMIT 1',
            $name
        );

        $option_value = $wpdb->get_var( $sql );

        if ( ! is_string( $option_value ) ) {
            return '';
        }

        return $option_value;
    }

    public function addOptionsPage() : void {
        add_submenu_page(
            '',
            'Directory Deployment Options',
            'Directory Deployment Options',
            'manage_options',
            'wp2static-addon-directory-deployment',
            [ $this, 'renderDirectoryDeployerPage' ]
        );
    }
}

