<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'form'    => $form->createView(),
            'target'  => $target,
        ]);
    }

    /**
     * @Route("/chat/refresh/{id}", name="chat_refresh")
     */
    public function refresh(Request $request, User $target, ChatRepository $chatRepository)
    {
        $lastChatId = $request->get('lastChatId', null);

        $lastChat = $lastChatId ? $chatRepository->findOneById() : null;

        $results = $chatRepository->getPreviousChats($this->getUser(), $target, $lastChat);

        $isNew = $results ? $results[0]->isNew() : false;

        $em = $this->getDoctrine()->getManager();

        foreach ($results as $chat) {
            $chat->setStatus(Chat::STATUS_READ);
            $em->persist($chat);
        }

        $em->flush();

        return new JsonResponse([
            'html'   => $this->renderView('chat/search.html.twig', ['results' => $results]),
            'is_new' => $isNew,
        ]);
    }

    /**
     * @Route("/chat/search/{id}/{page}", name="chat_search", defaults={"page"=1})
     */
    public function search(User $target, $page = 1)
    {
        return parent::doSearch(Chat::class, ['target_id' => $target->getId()], $page);
    }

    /**
     * @Route("/chat/post/{id}", name="chat_post")
     */
    public function post(Request $request, User $target)
    {
        $content = $request->get('content');

        $chat = new Chat();
        $chat
            ->setUser($this->getUser())
            ->setTarget($target)
            ->setContent($content);

        $em = $this->getDoctrine()->getManager();
        $em->persist($chat);
        $em->flush();

        return new JsonResponse(200);
    }
}
