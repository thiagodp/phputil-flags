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

    describe( 'removeAll()', function() {
        it( 'can remove all the flags', function() {
            $storage = new InMemoryStorage();
            $storage->set( 'foo', new FlagData( 'foo', false, new FlagMetadata() ) );
            $storage->set( 'bar', new FlagData( 'bar', false, new FlagMetadata() ) );
            expect( $storage->count() )->toBe( 2 );
            $storage->removeAll();
            expect( $storage->count() )->toBe( 0 );
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
