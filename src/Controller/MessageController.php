<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Form\MessageType;

class MessageController extends SearchController
{
    /**
     * @Route("/message/list", name="message_list")
     */
    public function list()
    {
        return $this->render('message/list.html.twig');
    }

    /**
     * @Route("/message/show/{id}", name="message_show")
     */
    public function show(Request $request, User $author, MessageRepository $messageRepository)
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->getUsersMessages($author, $this->getUser());

        $form = $this->createForm(MessageType::class, new Message(), [
            'user'   => $this->getUser(),
            'target' => $author,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($message);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Message envoyé.');

            return $this->redirectToRoute('message_show', ['id' => $author->getId()]);
        }

        return $this->render('message/show.html.twig', [
            'messages' => $messages,
            'author'   => $author,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/message/search/{page}", name="message_search", defaults={"page"=1})
     */
    public function search(Request $request, $page = 1)
    {
        return parent::doSearch(Message::class, $request->query->all(), $page);
    }
}
