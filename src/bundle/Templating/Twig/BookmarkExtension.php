<?php

namespace Edgar\EzUIBookmarkBundle\Templating\Twig;

use Edgar\EzUIBookmarkBundle\Service\BookmarkService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use Twig_Extension;
use Twig_SimpleFunction;

class BookmarkExtension extends Twig_Extension
{
    /** @var BookmarkService  */
    protected $bookmarkService;

    /** @var LocationService  */
    protected $locationService;

    /** @var SearchService  */
    protected $searchService;

    public function __construct(
        BookmarkService $bookmarkService,
        LocationService $locationService,
        SearchService $searchService
    ) {
        $this->bookmarkService = $bookmarkService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'is_already_bookmarked',
                [$this, 'isAlreadyBookmarked'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'get_path_locations',
                [$this, 'getPathLocations'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function isAlreadyBookmarked(int $userId, ?int $locationId, array $parameters = []): bool
    {
        return $this->bookmarkService->alreadyRegistered($userId, $locationId);
    }

    public function getPathLocations(int $locationId): array
    {
        $pathLocations = array();

        try {
            $location = $this->locationService->loadLocation($locationId);
            if ($location) {
                return $this->loadPathLocations($location);
            }
        } catch (UnauthorizedException | NotFoundException $e) {
        }

        return $pathLocations;
    }

    private function loadPathLocations(Location $location)
    {
        $locationQuery = new LocationQuery([
            'filter' => new Ancestor($location->pathString),
            'sortClauses' => [new Path()],
        ]);

        try {
            $searchResult = $this->searchService->findLocations($locationQuery);
        } catch(InvalidArgumentException $e) {
            return [];
        }

        return array_map(function (SearchHit $searchHit) {
            return $searchHit->valueObject;
        }, $searchResult->searchHits);
    }
}
