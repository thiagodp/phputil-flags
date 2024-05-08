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

    public function isEnabled( string $flag, array $strategies = [] ): bool {
        if ( count( $strategies ) < 1 ) {
            return $this->allEnabled( $flag, $this->verificationStrategies );
        }
        return $this->allEnabled( $flag, $strategies );
    }

    public function enable( string $flag ): void {
        $this->setEnabled( $flag, true );
    }

    public function disable( string $flag ): void {
        $this->setEnabled( $flag, false );
    }

    public function setEnabled( string $flagData, bool $enabled ): void {
        $flagData = $this->storage->touch( $flagData, $enabled );
        $this->listeners->notify( CHANGE_EVENT, $flagData );
    }

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

    /**
     * Returns `true` if all the strategies return `true.
     *
     * @param array<int, FlagVerificationStrategy> $strategies Verification strategies.
     */
    protected function allEnabled( string $flag, array $strategies ): bool {
        if ( empty( $strategies ) ) {
            return false;
        }
        foreach ( $strategies as $st ) {
            if ( ! $st->isEnabled( $flag ) ) {
                return false;
            }
        }
        return true;
    }
}
