<?php

declare(strict_types=1);

namespace App\DataModel;

class Metadata
{
    /**
     * @var Snapshot
     */
    protected $latestSnapshot;

    /**
     * @return Snapshot
     */
    public function getSnapshot()
    {
        return $this->latestSnapshot;
    }
}
