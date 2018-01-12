<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\View;
use App\Entity\User;
use App\Repository\ViewRepository;

class ViewController extends AbstractController
{
    /**
     * @Route("/view", name="view_list")
     */
    public function list()
    {
        return $this->render('view/list.html.twig');
    }

    /**
     * @Route("/view/search", name="view_search")
     */
    public function search()
    {
        $views = $this->getDoctrine()->getRepository(View::class)->findBy(['target' => $this->getUser()], ['updatedAt' => 'DESC']);

        return $this->render('view/search.html.twig', [
            'views' => $views,
        ]);
    }
}
