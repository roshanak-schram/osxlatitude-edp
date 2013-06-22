<?php namespace CFPropertylist;

//-- Includes
require_once 'CFPropertyList/CFPropertyList.php';

//-- Defines
define('DS', DIRECTORY_SEPARATOR);

//-- Options
$directory = __DIR__;
$exclude   = array( '.', '..', '.svn', 'CFPropertyList' );

//-- Logic
if ( $handle = opendir($directory) ) {
	echo 'Scanning directory: ' . $directory . PHP_EOL;

	while ( false !== ( $entry = readdir( $handle ) ) ) {
		if ( is_dir( $entry ) ) {
			if ( !in_array( $entry, $exclude ) && strncmp( $entry, '.', strlen( '.' ) ) ) {
                $file = $directory . DS . $entry . DS . 'common' . DS . 'smbios.plist';

                if ( file_exists( $file ) ) {
                    $plist = new CFPropertylist( $file );
                    $dict  = $plist->getValue( true );

                    $valueSet = false;

                    foreach ( $dict as $key => $value ) {
                        if ( $key == 'EDPmodel' ) {
                            $value->setValue( $entry );

                            $valueSet = true;
                        }
                    }

                    if ( !$valueSet ) {
                        $dict->add( 'EDPmodel', new CFString( $entry ) );
                    }

                    $plist->save( $file );

    				echo 'OK: ' . $file . PHP_EOL;
                }
			}
		}
	}
} else {
	echo 'Cannot read directory... try elevating permissions!' . PHP_EOL;
}