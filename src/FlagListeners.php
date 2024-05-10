<?php
namespace phputil\flags;

class FlagListeners {

    /** @var array<int, FlagListener> */
    private array $listeners = [];

    /**
     * Returns all the listeners.
     *
     * @return array<int, FlagListener>
     */
    public function getAll(): array {
        return $this->listeners;
    }

    public function add( FlagListener $listener ): FlagListeners {
        $this->listeners [] = $listener;
        return $this;
    }

    public function remove( FlagListener $listener ): FlagListeners {
        $indexFound = array_search( $listener, $this->listeners, true );
        if ( $indexFound !== false ) {
            unset( $this->listeners[ $indexFound ] );
            $this->listeners = array_values( $this->listeners );
        }
        return $this;
    }

    public function addAll( ...$listeners ): FlagListeners {
        array_push( $this->listeners, ...$listeners );
        return $this;
    }

    public function removeAll(): FlagListeners {
        $this->listeners = [];
        return $this;
    }

    public function notify( string $event, FlagData $flag ): void {
        foreach ( $this->listeners as $l ) {
            $l->notify( $event, $flag );
        }
    }
}
?>