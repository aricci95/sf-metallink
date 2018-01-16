<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Search\UserSearchType;
use App\Entity\User;
use App\Entity\View;
use App\Repository\UserRepository;
use App\Repository\ViewRepository;
use App\Service\LinkService;
use App\Service\IndicatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
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
    public function show(User $user, IndicatorService $indicatorService, ViewRepository $viewRepository)
    {
        if ($this->getUser() && $user != $this->getUser()) {
            $view = $viewRepository->findOneBy([
                'user'   => $this->getUser(),
                'target' => $user,
            ]);

            if (!$view) {
                $view = new View();

                $indicatorService->flush($user);
            }

            $view
                ->setUser($this->getUser())
                ->setTarget($user)
                ->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($view);
            $em->flush();
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/search/{page}", name="user_search", defaults={"page"=1})
     */
    public function search(Request $request, LinkService $linkService, UserRepository $userRepository, $page = 1)
    {
        $count = $userRepository->searchCount($request->query->all(), $linkService->getBlackList());

        $users = [];

        $pageSize = $this->getParameter('page_size');

        if ($count) {
            $users = $userRepository->search($request->query->all(), $linkService->getBlackList(), $page, $pageSize);
        }

        return new JsonResponse([
            'html'     => $this->renderView('user/search.html.twig', ['users' => $users]),
            'nextPage' => ($count > $page * $pageSize) ? $page + 1 : false,
        ]);
    }
}
