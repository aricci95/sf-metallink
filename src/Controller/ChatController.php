<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use App\Form\ChatType;

class ChatController extends SearchController
{
    /**
     * @Route("/chat/dialog", name="chat_dialog")
     */
    public function dialog(Request $request, ChatRepository $chatRepository, UserRepository $userRepository)
    {
        $target = $userRepository->findOneById($request->get('id'));

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

        $lastChat = $lastChatId ? $chatRepository->findOneById($lastChatId) : null;

        $results = $chatRepository->getPreviousChats($this->getUser(), $target, $lastChat);

        $isNew = false;
        foreach ($results as $result) {
            if ($result->getTarget() == $this->getUser() && $result->isNew()) {
                $isNew = true;
            }
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($results as $chat) {
            if ($this->getUser() == $chat->getTarget()) {
                $chat->setStatus(Chat::STATUS_READ);
                $em->persist($chat);
            }
        }

        $em->flush();

        return new JsonResponse([
            'html'       => $results ? $this->renderView('chat/search.html.twig', ['results' => $results]) : null,
            'isNew'      => $isNew,
            'lastChatId' => $results ? array_reverse($results)[0]->getId() : null,
        ]);
    }

    /**
     * @Route("/chat/has-new", name="chat_has_new")
     */
    public function hasNewChats(ChatRepository $chatRepository)
    {
        return new JsonResponse([
            'userIds' => $chatRepository->hasNewChats($this->getUser()),
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
