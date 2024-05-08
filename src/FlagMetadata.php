<?php
namespace phputil\flags;

use DateTime;

class FlagMetadata {

    public int $id;

    public DateTime $createdAt;

    public DateTime $updatedAt;

    public int $accessCount = 0;

    /** @var array<int, string> */
    public array $tags = [];

    public function __construct(
        int $id = 0,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        int $accessCount = 0,
        array $tags = []
    ) {
        $this->id = $id;
        $now = new DateTime();
        $this->createdAt = $createdAt ?? $now;
        $this->updatedAt = $updatedAt ?? $now;
        $this->accessCount = $accessCount;
        $this->tags = $tags;
    }

    public function updateAccess() {
        $this->updatedAt = new DateTime();
        $this->accessCount++;
    }
}
?>