<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\PictureType;

class PictureController extends AbstractController
{
    /**
     * @Route("/picture/edit", name="picture_edit")
     */
    public function edit(Request $request)
    {
        $form = $this->createForm(PictureType::class, $this->getUser());

        $form->handleRequest($request);

        return $this->render('picture/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/message/search", name="message_search")
     */
    public function search()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(['target' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('message/search.html.twig', [
            'messages' => $messages,
        ]);
    }
}
