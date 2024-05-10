<?php
use phputil\flags\FlagListener;
use phputil\flags\FlagListeners;
use phputil\flags\FlagData;

describe( 'FlagListeners', function() {

    it( 'starts with no listeners', function() {
        $fl = new FlagListeners();
        expect( $fl->getAll() )->toBeEmpty();
    } );

    it( 'can add a listener', function() {
        $fl = new FlagListeners();
        $listener = new class implements FlagListener {
            public function notify( string $event, FlagData $flag ): void {
            }
        };
        expect( $fl->getAll() )->toHaveLength( 0 );
        $fl->add( $listener );
        expect( $fl->getAll() )->toHaveLength( 1 );
    } );

    it( 'can add all the given listeners', function() {
        $fl = new FlagListeners();
        $listenerA = new class implements FlagListener {
            public function notify( string $event, FlagData $flag ): void {
            }
        };
        $listenerB = new class implements FlagListener {
            public function notify( string $event, FlagData $flag ): void {
            }
        };
        expect( $fl->getAll() )->toHaveLength( 0 );
        $fl->addAll( $listenerA, $listenerB );
        expect( $fl->getAll() )->toHaveLength( 2 );
    } );


    it( 'can remove a listener', function() {
        $fl = new FlagListeners();
        $listener = new class implements FlagListener {
            public function notify( string $event, FlagData $flag ): void {
            }
        };
        $fl->add( $listener );
        expect( $fl->getAll() )->toHaveLength( 1 );
        $fl->remove( $listener );
        expect( $fl->getAll() )->toHaveLength( 0 );
    } );

} );
