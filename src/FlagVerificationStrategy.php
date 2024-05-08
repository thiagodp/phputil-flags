<?php
namespace phputil\flags;

interface FlagVerificationStrategy {
    public function isEnabled( string $key ): bool;
}
?>