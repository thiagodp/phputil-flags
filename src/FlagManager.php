<?php

namespace phputil\flags;

const CHANGE_EVENT = 'changed';
const REMOVAL_EVENT = 'removed';

class FlagManager {

    /** @var array<int, FlagVerificationStrategy> */
    private array $verificationStrategies;

    private FlagStorage $storage; // TODO: make public readonly when migrating to PHP 8.0

    private FlagListeners $listeners; // TODO: make public readonly when migrating to PHP 8.0

    /**
     * Creates a flag manager.
     *
     * @param ?FlagStorage $storage Storage
     * @param array<int, FlagVerificationStrategy> $strategies Verification strategies.
     */
    public function __construct(
        ?FlagStorage $storage = null,
        array $strategies = []
    ) {
        $this->storage = $storage ?? new InMemoryStorage();
        if ( empty( $strategies ) ) {
            $this->verificationStrategies = [ new StorageBasedVerificationStrategy( $this->storage ) ];
        } else {
            $this->verificationStrategies = $strategies;
        }
        $this->listeners = new FlagListeners();
    }

    /**
     * Checks if a flag is enabled, by using the default strategies or the strategies given.
     *
     * @param string $flag Flag to check
     * @param array<int, FlagVerificationStrategy> $strategies Stratagies to use. When not defined, the default ones are used.
     * @return bool
     *
     * @throws FlagException
     */
    public function isEnabled( string $flag, array $strategies = [] ): bool {
        // Use the default strategies when no strategies are given
        if ( count( $strategies ) < 1 ) {
            return $this->allEnabled( $flag, $this->verificationStrategies );
        }
        return $this->allEnabled( $flag, $strategies );
    }

    /**
     * Enables the given flag. The flag is created if it does not exist.
     *
     * @param string $flag Flag to enable.
     **/
    public function enable( string $flag ): void {
        $this->setEnabled( $flag, true );
    }

    /**
     * Disables the given flag. The flag is created if it does not exist.
     *
     * @param string $flag Flag to disable.
     **/
    public function disable( string $flag ): void {
        $this->setEnabled( $flag, false );
    }

    /**
     * Sets the enabled state of the given flag. The flag is created if it does not exist.
     *
     * @param string $flag Flag to disable.
     **/
    public function setEnabled( string $flagData, bool $enabled ): void {
        $flagData = $this->storage->touch( $flagData, $enabled );
        $this->listeners->notify( CHANGE_EVENT, $flagData );
    }

    /**
     * Removes the given flag.
     *
     * @param string $flag Flag to remove.
     * @return bool `true` if it was found and removed.
     **/
    public function remove( string $flag ): bool {
        $flagData = $this->storage->get( $flag );
        if ( $flagData === null ) {
            return false;
        }

        $hasRemoved = $this->storage->remove( $flag );
        if ( $hasRemoved ) {
            $this->listeners->notify( REMOVAL_EVENT, $flagData );
        }
        return $hasRemoved;
    }

    public function getStorage(): FlagStorage {
        return $this->storage;
    }

    public function getListeners(): FlagListeners {
        return $this->listeners;
    }

    public function addListener( FlagListener $listener ): FlagManager {
        $this->getListeners()->add( $listener );
        return $this;
    }

    /**
     * Returns `true` if all the strategies return `true.
     *
     * @param array<int, FlagVerificationStrategy> $strategies Verification strategies.
     */
    protected function allEnabled( string $flag, array $strategies ): bool {
        foreach ( $strategies as $st ) {
            if ( ! $st->isEnabled( $flag ) ) {
                return false;
            }
        }
        return count( $strategies ) > 0;
    }
}
