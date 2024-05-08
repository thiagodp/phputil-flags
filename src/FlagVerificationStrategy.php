<?php
namespace phputil\flags;

interface FlagVerificationStrategy {
    function isEnabled( string $key ): bool;
}
?>