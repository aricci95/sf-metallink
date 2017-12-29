<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Link;
use App\Service\LinkService;

class LinkController extends AbstractController
{
    /**
     * @Route("/link/create", name="link_create")
     */
    public function create(Request $request)
    {
        $target = $this->getDoctrine()->getRepository(User::class)->findOneById($request->get('target_id'));

        $link = new Link();
        $link
            ->setUser($this->getUser())
            ->setTarget($target)
            ->setStatus(Link::STATUS_SENT);

        $linkRepository = $this->getDoctrine()->getRepository(Link::class);

        $this->getDoctrine()->getManager()->persist($link);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('link/panel.html.twig', [
            'link' => $link,
        ]);
    }

    /**
     * @Route("/link/validate/{link}", name="link_validate")
     */
    public function validate(Link $link)
    {
        $link->setStatus(Link::STATUS_VALIDATED);

        $linkRepository = $this->getDoctrine()->getRepository(Link::class);

        $this->getDoctrine()->getManager()->persist($link);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('link/panel.html.twig', [
            'link' => $link,
        ]);
    }
}
