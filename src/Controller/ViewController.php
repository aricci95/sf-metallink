<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\View;
use App\Entity\User;
use App\Repository\ViewRepository;

class ViewController extends SearchController
{
    /**
     * @Route("/view/list", name="view_list")
     */
    public function list()
    {
        return $this->render('view/list.html.twig');
    }

    /**
     * @Route("/view/search/{page}", name="view_search", defaults={"page"=1})
     */
    public function search(Request $request, $page = 1)
    {
        return parent::doSearch(View::class, $request->query->all(), $page);
    }
}
