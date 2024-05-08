<?php
namespace phputil\flags;

class FlagData {

    public string $key;
    public bool $enabled;
    public FlagMetadata $metadata;

    public function __construct(
        string $key,
        bool $enabled,
        FlagMetadata $metadata
    ) {
        $this->key = $key;
        $this->enabled = $enabled;
        $this->metadata = $metadata;
    }

    public function updateAccess(): FlagData {
        $this->metadata->updateAccess();
        return $this;
    }

    public function setEnabled( bool $enabled ): FlagData {
        $this->enabled = $enabled;
        return $this;
    }
}

?>