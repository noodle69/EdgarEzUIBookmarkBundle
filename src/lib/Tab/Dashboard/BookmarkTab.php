<?php

namespace Edgar\EzUIBookmark\Tab\Dashboard;

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;

class BookmarkTab extends AbstractTab implements OrderedTabInterface
{
    public function getIdentifier(): string
    {
        return 'bookmark';
    }

    public function getName(): string
    {
        return
            $this->translator->trans('tab.name.bookmark', [], 'bookmark');
    }

    public function getOrder(): int
    {
        return 300;
    }

    public function renderView(array $parameters): string
    {
        return $this->twig->render('@EdgarEzUIBookmark/bookmark/dashboard/tab/bookmark.html.twig', [
        ]);
    }
}
