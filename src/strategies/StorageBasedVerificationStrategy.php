<?php
namespace phputil\flags;

/**
 * Storage-based flag verification strategy.
 */
class StorageBasedVerificationStrategy implements FlagVerificationStrategy {

    private FlagStorage $storage;

    /**
     * @param FlagStorage $storage Storage
     */
    public function __construct(
        FlagStorage $storage
    ) {
        $this->storage = $storage;
    }

    public function isEnabled( string $key ): bool {
        return $this->storage->touch( $key )->enabled;
    }
}