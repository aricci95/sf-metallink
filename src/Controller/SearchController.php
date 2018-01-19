<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\LinkService;
use App\Entity\User;

abstract class SearchController extends Controller
{
    protected $linkService;

    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    public function doSearch($entity, array $params = [], $page = 1)
    {
        $entityName = strtolower(array_reverse(explode('\\', $entity))[0]);

        $repository = $this->getDoctrine()->getManager()->getRepository($entity);

        $blacklist = $this->linkService->getBlackList();

        $count = $repository->searchCount($this->getUser(), $params, $blacklist);

        $results = [];

        $pageSize = $this->getParameter('page_size');

        if ($count) {
            $results = $repository->search($this->getUser(), $params, $blacklist, $page, $pageSize);
        }
        
        /*
        for ($i=0; $i<=250; $i++) {
            $results[] = $results[0];
        }*/

        return new JsonResponse([
            'html'     => $this->renderView($entityName . '/search.html.twig', ['results' => $results]),
            'nextPage' => ($count > $page * $pageSize) ? $page + 1 : false,
        ]);
    }
}
