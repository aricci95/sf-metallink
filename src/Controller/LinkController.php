<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\User;
use App\Entity\Link;

class LinkController extends AbstractController
{
    /**
     * @Route("/link/create", name="link_create")
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request)
    {
        $target = $this->getDoctrine()->getRepository(User::class)->findOneById($request->get('target_id'));

        $link = new Link();
        $link
            ->setUser($this->getUser())
            ->setTarget($target);

        return $this->update($link, Link::STATUS_PENDING);
    }

    /**
     * @Route("/link/validate/{link}", name="link_validate")
     * @Security("has_role('ROLE_USER')")
     */
    public function validate(Link $link)
    {
        return $this->update($link, Link::STATUS_ACCEPTED);
    }

    /**
     * @Route("/link/blacklist/{link}", name="link_blacklist")
     * @Security("has_role('ROLE_USER')")
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
            'current_link' => $link,
        ]);
    }

    /**
     * @Route("/link/{status}", name="link_list")
     */
    public function list($status)
    {
        return $this->render('link/list.html.twig', [
            'status' => $status,
        ]);
    }

    /**
     * @Route("/link/search/{status}", name="link_search")
     * @Security("has_role('ROLE_USER')")
     */
    public function search($status)
    {
        $links = $this->getDoctrine()->getRepository(Link::class)->findBy([
            'target' => $this->getUser(),
            'status' => $status
        ], ['createdAt' => 'DESC']);

        return $this->render('link/search.html.twig', [
            'links' => $links,
        ]);
    }
}
