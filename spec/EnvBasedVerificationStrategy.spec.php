<?php
use phputil\flags\EnvBasedVerificationStrategy;

describe( 'EnvBasedVerificationStrategy', function() {

    it( 'identifies when a flag is on', function() {
        $key = '_foo_';
        putenv( "$key=1" );
        $st = new EnvBasedVerificationStrategy();
        expect( $st->isEnabled( $key ) )->toBeTruthy();
    } );

    it( 'identifies when a flag is off', function() {
        $key = '_foo_';
        putenv( "$key=0" );
        $st = new EnvBasedVerificationStrategy();
        expect( $st->isEnabled( $key ) )->toBeFalsy();
    } );

} );