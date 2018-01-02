<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message_list")
     */
    public function list()
    {
        return $this->render('message/list.html.twig');
    }

    /**
     * @Route("/message/show/{id}", name="message_show")
     */
    public function show(Message $message)
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/message/search", name="message_search")
     */
    public function search()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findByUser($this->getUser());

        return $this->render('message/search.html.twig', [
            'messages' => $messages,
        ]);
    }
}
