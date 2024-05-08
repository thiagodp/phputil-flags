<?php
namespace phputil\flags;

class ConsoleListener implements FlagListener {
    public function notify( string $event, FlagData $flag ): void {
        echo '[', $event, '] - ', $flag->key, PHP_EOL,
        "\t", json_encode( $flag );
    }
}
?>