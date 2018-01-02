<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Link;

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
            ->setTarget($target);

        return $this->update($link, Link::STATUS_SENT);
    }

    /**
     * @Route("/link/validate/{link}", name="link_validate")
     */
    public function validate(Link $link)
    {
        return $this->update($link, Link::STATUS_ACCEPTED);
    }

    /**
     * @Route("/link/blacklist/{link}", name="link_blacklist")
     */
    public function blacklist(Link $link)
    {
        return $this->update($link, Link::STATUS_BLACKLISTED);
    }

    private function update(Link $link, $status)
    {
        $link->setStatus($status);

        $this->getDoctrine()->getManager()->persist($link);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('link/panel.html.twig', [
            'link' => $link,
        ]);
    }
}
