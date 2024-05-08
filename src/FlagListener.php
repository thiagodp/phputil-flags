<?php
namespace phputil\flags;

interface FlagListener {
    public function notify( string $event, FlagData $flag ): void;
}

?>