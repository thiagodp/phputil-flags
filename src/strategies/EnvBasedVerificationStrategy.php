<?php
namespace phputil\flags;

/**
 * Strategy that checks flags using environment variables.
 */
class EnvBasedVerificationStrategy implements FlagVerificationStrategy {

    public function isEnabled( string $key ): bool {
        $value = $_ENV[ $key ] ?? ( getenv( $key ) ?: false );
        return $value == '1' || $value == 'true';
    }
}
?>