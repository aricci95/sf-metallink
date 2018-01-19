<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Form\ChatType;

class ChatController extends SearchController
{
    /**
     * @Route("/chat/dialog/{id}", name="chat_dialog")
     */
    public function dialog(Request $request, User $target, ChatRepository $chatRepository)
    {
        $form = $this->createForm(ChatType::class, new Chat(), [
            'user'   => $this->getUser(),
            'target' => $target,
        ]);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $chat = $form->getData();

            $em->persist($chat);
            $em->flush();

            return new JsonResponse(200);
        }

        return $this->render('chat/dialog.html.twig', [
            'results' => array_reverse($chatRepository->getUsersChats($this->getUser(), $target)),
            'form'    => $form->createView(),
            'target'  => $target,
        ]);
    }

    /**
     * @Route("/chat/search/{page}", name="chat_search", defaults={"page"=1})
     */
    public function search(Request $request, $page = 1)
    {
        $params = $request->query->all();

        return parent::doSearch(Chat::class, $params, $page);
    }
}
