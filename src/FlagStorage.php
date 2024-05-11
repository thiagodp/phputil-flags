<?php
namespace phputil\flags;

interface FlagStorage {

    /**
     * Retrieves the enabled state of a flag.
     *
     * @param string $key Key
     * @return bool
     *
     * @throws FlagException
     */
    public function isEnabled( string $key ): bool;

    /**
     * Retrieves a flag with the given key, without changing the flag.
     *
     * @param string $key Key
     * @return FlagData|null
     *
     * @throws FlagException
     */
    public function get( string $key ): ?FlagData;

    /**
     * Retrieves and updates a flag with the given key, if it exists.
     * Creates a flag when the given key does not exist.
     * If `$enabled` is given and it is not `null`, updates the flag accordingly.
     *
     * @param string $key Key
     * @param bool|null $enabled Enabled flag. When null, it is ignored.
     *
     * @throws FlagException
     */
    public function touch( string $key, ?bool $enabled = null ): ?FlagData;

    /**
     * Sets a flag.
     *
     * @param string $key Key
     * @param FlagData $data Flag data
     * @return bool `true` if the key was found and updated.
     *
     * @throws FlagException
     */
    public function set( string $key, FlagData $data ): bool;

    /**
     * Removes a flag.
     *
     * @param string $key Key
     * @return bool Indicates if the flag was removed.
     *
     * @throws FlagException
     */
    public function remove( string $key ): bool;

    /**
     * Removes all the flags.
     *
     * @throws FlagException
     */
    public function removeAll(): void;

    /**
     * Returns all the flags.
     *
     * @param array<string, mixed> $options Options. Default to [].
     * @return array<int, FlagData>
     *
     * @throws FlagException
     */
    public function getAll( array $options = [] ): array;

    /**
     * Returns the number of stored flags.
     *
     * @param array<string, mixed> $options Options. Default to [].
     * @return int
     *
     * @throws FlagException
     */
    public function count( array $options = [] ): int;

}
?>