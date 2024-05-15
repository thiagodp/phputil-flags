<?php
use phputil\flags\FlagData;
use phputil\flags\FlagMetadata;
use phputil\flags\InMemoryStorage;

describe( 'InMemoryStorage', function() {

    it( 'starts with no flags', function() {
        $storage = new InMemoryStorage();
        expect( $storage->getAll() )->toBeEmpty();
    } );

    describe( 'remove()', function() {

        it( 'returns true when it removes a flag', function() {
            $storage = new InMemoryStorage();
            $flagData = new FlagData( 'foo', false, new FlagMetadata() );
            $storage->set( 'foo', $flagData );
            expect( $storage->getAll() )->not->toBeEmpty();
            $result = $storage->remove( 'foo' );
            expect( $result )->toBeTruthy();
            expect( $storage->getAll() )->toBeEmpty();
        } );

        it( 'returns false when the flag does not exist', function() {
            $storage = new InMemoryStorage();
            $result = $storage->remove( 'foo' );
            expect( $result )->toBeFalsy();
        } );

    } );

    it( 'generates an incremental id every new flag', function() {
        $storage = new InMemoryStorage();
        $foo = $storage->touch( 'foo' );
        expect( $foo->metadata->id )->toBe( 1 );
        $bar = $storage->touch( 'bar' );
        expect( $bar->metadata->id )->toBe( 2 );
    } );

} );
