<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message_list")
     */
    public function list()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findOneByUser($this->getUser());

        return $this->render('message/list.html.twig', [
            'messages' => $messages,
        ]);
    }
}
