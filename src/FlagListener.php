<?php
namespace phputil\flags;

interface FlagListener {
    function notify( string $event, FlagData $flag ): void;
}

?>