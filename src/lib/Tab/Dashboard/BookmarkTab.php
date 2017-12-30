<?php

namespace Edgar\EzUIBookmark\Tab\Dashboard;

use Edgar\EzUIBookmarkBundle\Service\BookmarkService;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class BookmarkTab extends AbstractTab implements OrderedTabInterface
{
    /** @var BookmarkService  */
    protected $bookmarkService;

    public function __construct(
        Environment $twig, TranslatorInterface $translator,
        BookmarkService $bookmarkService
    ) {
        parent::__construct($twig, $translator);

        $this->bookmarkService = $bookmarkService;
    }

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
        $bookmarks = $this->bookmarkService->listBookmarks();

        return $this->twig->render('@EdgarEzUIBookmark/bookmark/dashboard/tab/bookmark.html.twig', [
            'bookmarks' => $bookmarks,
        ]);
    }
}
