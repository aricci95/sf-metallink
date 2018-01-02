<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;

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
    public function show(User $user, MessageRepository $messageRepository)
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->getUsersMessages($user, $this->getUser());

        return $this->render('message/show.html.twig', [
            'messages' => $messages,
            'author'   => $messages[0]->getUser(),
        ]);
    }

    /**
     * @Route("/message/search", name="message_search")
     */
    public function search()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findByTarget($this->getUser());

        return $this->render('message/search.html.twig', [
            'messages' => $messages,
        ]);
    }
}
