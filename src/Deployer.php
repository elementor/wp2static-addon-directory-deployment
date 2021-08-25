<?php

namespace WP2StaticCopy;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use WP2Static\WsLog;

function rrmdir( string $path ) : void
{

    if( trim( pathinfo( $path, PATHINFO_BASENAME ), '.' ) === '' )
        return;

    if( is_dir( $path ) )
    {
        array_map( 'rrmdir', glob( $path . DIRECTORY_SEPARATOR . '{,.}*', GLOB_BRACE | GLOB_NOSORT ) );
        @rmdir( $path );
    }

    else
        @unlink( $path );

}

function xcopy($source, $dest, $permissions = 0755)
{
    $sourceHash = hashDirectory($source);
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        if($sourceHash != hashDirectory($source."/".$entry)){
             xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
    }

    // Clean up
    $dir->close();
    return true;
}

// In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated

function hashDirectory($directory){
    if (! is_dir($directory)){ return false; }

    $files = array();
    $dir = dir($directory);

    while (false !== ($file = $dir->read())){
        if ($file != '.' and $file != '..') {
            if (is_dir($directory . '/' . $file)) { $files[] = hashDirectory($directory . '/' . $file); }
            else { $files[] = md5_file($directory . '/' . $file); }
        }
    }

    $dir->close();

    return md5(implode('', $files));
}

class Deployer {

    const DEFAULT_NAMESPACE = 'wp2static-addon-copy/default';

    // prepare deploy, if modifies URL structure, should be an action
    // $this->prepareDeploy();

    // options - load from addon's static methods

    public function __construct() {}

    public function uploadFiles( string $processed_site_path ) : void {
        // check if dir exists
        if ( ! is_dir( $processed_site_path ) ) {
            $err = 'Processed folder does not exist: ' . $processed_site_path;
            \WP2Static\WsLog::l( $err );
            return;
        }

        $targetFolder = Controller::getValue( 'copyTargetFolder' );
        if (empty($targetFolder)) {
            $err = 'You must specify the target folder in WP2Static > Addons > Copy Deployment > Configure';
            \WP2Static\WsLog::l( $err );
            return;
        }
        if ( ! is_dir( $targetFolder ) ) {
            $err = 'Target folder does not exist: ' . $targetFolder;
            \WP2Static\WsLog::l( $err );
            return;
        }

        $cleanTarget = intval( Controller::getValue( 'copyRemoveTarget' ) ) !== 0;
        if ($cleanTarget) {
          $err = 'Cleaning '. $targetFolder;
          \WP2Static\WsLog::l( $err );
          rrmdir($targetFolder);
          mkdir($targetFolder);
        }

        $err = 'Copying '. $processed_site_path . ' to ' . $targetFolder;
        \WP2Static\WsLog::l( $err );
        xcopy($processed_site_path, $targetFolder);
        $extraFolder = Controller::getValue( 'copyExtraFolder' );
        if (!empty($extraFolder)) {
            if ( ! is_dir( $extraFolder ) ) {
                $err = 'Extra folder does not exist: ' . $extraFolder;
                \WP2Static\WsLog::l( $err );
                return;
            }
            $err = 'Copying '. $extraFolder . ' to ' . $targetFolder;
            \WP2Static\WsLog::l( $err );
            xcopy($extraFolder, $targetFolder);
        }
    }
}
