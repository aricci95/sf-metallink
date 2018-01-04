<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Search\UserSearchType;
use App\Entity\User;
use App\Entity\View;
use App\Repository\UserRepository;
use App\Service\LinkService;

class UserController extends AbstractController
{
    /**
     * @Route("/user/list", name="user_list")
     */
    public function index()
    {
        return $this->render('user/list.html.twig', [
            'form' => $this->createForm(UserSearchType::class, new User())->createView()
        ]);
    }

    /**
     * @Route("/user/show/{id}", name="user_show")
     */
    public function show(User $user)
    {
        if ($this->getUser() && $user != $this->getUser()) {
            $view = $this->getDoctrine()->getManager()->getRepository(View::class)->findOneBy([
                'user'   => $this->getUser(),
                'target' => $user,
            ]);

            if (!$view) {
                $view = new View();
                $view
                    ->setUser($this->getUser())
                    ->setTarget($user);

                $em = $this->getDoctrine()->getManager();

                $em->persist($view);
                $em->flush();
            }
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/search", name="user_search")
     */
    public function search(Request $request, LinkService $linkService)
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->search($request->query->all(), $linkService->getBlackList());

        return $this->render('user/search.html.twig', [
            'users' => $users,
        ]);
    }
}
