<?php
namespace phputil\flags;

/**
 * In-memory storage. Useful for testing purposes.
 */
class InMemoryStorage implements FlagStorage {

    /** @var array<string, FlagData> */
    private array $flags = [];

    public function isEnabled( string $key ): bool {
        $flag = $this->get( $key );
        return $flag === null ? false : $flag->enabled;
    }

    /** @inheritDoc */
    public function get( string $key ): ?FlagData {
        return $this->flags[ $key ] ?? null;
    }

    /** @inheritDoc */
    public function touch( string $key, ?bool $enabled = null ): ?FlagData {
        $flag = $this->get( $key ) ??
            new FlagData( $key, false, new FlagMetadata() );

        $this->set( $key, $flag );

        if ( $enabled !== null ) {
            $flag->enabled = $enabled;
        }

        return $flag->updateAccess();
    }

    /** @inheritDoc */
    public function set( string $key, FlagData $flag ): void {
        $this->flags[ $key ] = $flag;
    }

    /** @inheritDoc */
    public function remove( string $key ): bool {
        if ( ! isset( $this->flags[ $key ] ) ) {
            return false;
        }
        unset( $this->flags[ $key ] );
        return true;
    }

    /** @inheritDoc */
    public function getAll( array $options = [] ): array {
        return array_values( $this->flags );
    }

    /** @inheritDoc */
    public function count(): int {
        return count( $this->flags );
    }
}
