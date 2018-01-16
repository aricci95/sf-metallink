<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\LinkService;

abstract class SearchController extends Controller
{
    private $linkService;

    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    public function doSearch($entity, array $params = [], $page = 1)
    {
        $entityName = strtolower(array_reverse(explode('\\', $entity))[0]);

        $repository = $this->getDoctrine()->getManager()->getRepository($entity);

        $blacklist = $this->linkService->getBlackList();

        $count = $repository->searchCount($params, $blacklist);

        $results = [];

        $pageSize = $this->getParameter('page_size');

        if ($count) {
            $results = $repository->search($params, $blacklist, $page, $pageSize);
        }

        return new JsonResponse([
            'html'     => $this->renderView($entityName . '/search.html.twig', ['results' => $results]),
            'nextPage' => ($count > $page * $pageSize) ? $page + 1 : false,
        ]);
    }
}
