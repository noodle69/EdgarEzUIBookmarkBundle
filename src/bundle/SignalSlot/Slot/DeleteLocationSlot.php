<?php

namespace Edgar\EzUIBookmarkBundle\SignalSlot\Slot;

use Edgar\EzUIBookmarkBundle\Service\BookmarkService;
use eZ\Publish\Core\SignalSlot\Slot;
use eZ\Publish\Core\SignalSlot\Signal;

class DeleteLocationSlot extends Slot
{
    private $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function receive(Signal $signal)
    {
        if (!$signal instanceof Signal\LocationService\DeleteLocationSignal) {
            return;
        }

        $this->bookmarkService->unRegisterLocation($signal->locationId);
    }
}
