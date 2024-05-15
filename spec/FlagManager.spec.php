<?php

use phputil\flags\FlagData;
use phputil\flags\FlagListener;
use phputil\flags\FlagManager;
use phputil\flags\FlagVerificationStrategy;

describe( 'FlagManager', function() {

    describe( 'isEnabled()', function() {

        it( 'does not create a flag when it is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
            $fm->isEnabled( 'foo' );
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
        } );

        it( 'returns false when the flag is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
            expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
        } );

        it( 'returns the enabled state if the flag exists', function() {
            $fm = new FlagManager();
            $fm->getStorage()->touch( 'foo', false );
            expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
            $fm->getStorage()->touch( 'bar', true );
            expect( $fm->isEnabled( 'bar' ) )->toBeTruthy();
        } );

        describe( 'given strategies', function () {

            it( 'can use the given strategies', function() {
                $fm = new FlagManager();
                $strategy = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };
                expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
                expect( $fm->isEnabled( 'foo', [ $strategy ] ) )->toBeTruthy();
            } );

            it( 'returns true when all the given strategies return true', function() {

                $strategy1 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };
                $strategy2 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };

                $fm = new FlagManager();
                expect( $fm->isEnabled( 'foo', [ $strategy1, $strategy2 ] ) )->toBeTruthy();
            } );

            it( 'returns false when one of the given strategies return false', function() {

                $strategy1 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };
                $strategy2 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return false;
                    }
                };

                $fm = new FlagManager();
                expect( $fm->isEnabled( 'foo', [ $strategy1, $strategy2 ] ) )->toBeFalsy();
            } );

            it( 'returns false when there are no default strategies and no strategies are given', function() {
                $fm = new FlagManager( null, [] );
                expect( $fm->isEnabled( 'foo', [] ) )->toBeFalsy();
            } );

        } );


        describe( 'default strategies', function () {

            it( 'returns true when all the default strategies return true', function() {

                $strategy1 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };
                $strategy2 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };

                $fm = new FlagManager( null, [ $strategy1, $strategy2 ] );
                expect( $fm->isEnabled( 'foo' ) )->toBeTruthy();
            } );


            it( 'returns false when one of the default strategies return false', function() {

                $strategy1 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return true;
                    }
                };
                $strategy2 = new class implements FlagVerificationStrategy {
                    public function isEnabled( string $key ): bool {
                        return false;
                    }
                };

                $fm = new FlagManager( null, [ $strategy1, $strategy2 ] );
                expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
            } );


            it( 'returns false when there are no default strategies', function() {
                $fm = new FlagManager( null, [] );
                expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
            } );

        } );

    } );


    describe( 'enable()', function() {

        it( 'creates a flag when it is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->count() )->toBe( 0 );
            $fm->enable( 'foo' );
            expect( $fm->getStorage()->count() )->toBe( 1 );
        } );

        it( 'enables a flag', function() {
            $fm = new FlagManager();
            $fm->getStorage()->touch( 'foo', false );
            expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
            $fm->enable( 'foo' );
            expect( $fm->isEnabled( 'foo' ) )->toBeTruthy();
        } );
    } );


    describe( 'disable()', function() {

        it( 'creates a flag when it is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->count() )->toBe( 0 );
            $fm->disable( 'foo' );
            expect( $fm->getStorage()->count() )->toBe( 1 );
        } );

        it( 'disables a flag', function() {
            $fm = new FlagManager();
            $fm->getStorage()->touch( 'foo', true );
            expect( $fm->isEnabled( 'foo' ) )->toBeTruthy();
            $fm->disable( 'foo' );
            expect( $fm->isEnabled( 'foo' ) )->toBeFalsy();
        } );
    } );


    describe( 'remove()', function() {

        it( 'does not create a flag when it is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
            $fm->remove( 'foo' );
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
        } );

        it( 'returns false when the flag is not found', function() {
            $fm = new FlagManager();
            expect( $fm->getStorage()->get( 'foo' ) )->toBeNull();
            expect( $fm->remove( 'foo' ) )->toBeFalsy();
        } );

        it( 'removes a enabled flag', function() {
            $fm = new FlagManager();
            $fm->getStorage()->touch( 'foo', true );
            expect( $fm->remove( 'foo') )->toBeTruthy();
        } );

        it( 'removes a disabled flag', function() {
            $fm = new FlagManager();
            $fm->getStorage()->touch( 'foo', false );
            expect( $fm->remove( 'foo') )->toBeTruthy();
        } );
    } );


    describe( 'notifies the listeners when', function() {

        $this->fm = new FlagManager();

        $this->listener = new class implements FlagListener {
            public $count = 0;
            public function notify(string $event, FlagData $flag): void {
                $this->count++;
            }
        };

        beforeEach( function() {
            $this->fm->getListeners()->add( $this->listener );
        } );

        afterEach( function() {
            $this->fm->getListeners()->removeAll();
        } );

        it( 'a flag is enabled', function() {
            $this->listener->count = 0;
            $this->fm->enable( 'foo' );
            expect( $this->listener->count )->toBe( 1 );
        } );

        it( 'a flag is disabled', function() {
            $this->listener->count = 0;
            $this->fm->disable( 'foo' );
            expect( $this->listener->count )->toBe( 1 );
        } );

        it( 'the enabled property is changed somehow', function() {
            $this->listener->count = 0;
            $this->fm->setEnabled( 'foo', ! $this->fm->isEnabled( 'foo' ) );
            expect( $this->listener->count )->toBe( 1 );
        } );

        it( 'a flag is removed', function() {
            $this->fm->enable( 'foo' );
            $this->listener->count = 0;
            $this->fm->remove( 'foo' );
            expect( $this->listener->count )->toBe( 1 );
        } );

    } );

    it( 'provides a method to add a listener directly', function() {

        $listener = new class implements FlagListener {
            public $count = 0;
            public function notify(string $event, FlagData $flag): void {
                $this->count++;
            }
        };

        $fm = new FlagManager();
        $fm->addListener( $listener );

        $fm->enable( 'foo' );

        expect( $listener->count )->toBeGreaterThan( 0 );
    } );

} );
?>