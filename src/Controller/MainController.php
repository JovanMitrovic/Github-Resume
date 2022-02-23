<?php

namespace App\Controller;

use App\GitHub\GitHubApiHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('homepage.html.twig', []);
    }

    /**
     * @Route("/create-user-cv", name="create_user_cv")
     * @Method({"POST"})
     */
    public function createUserCV(Request $request, GitHubApiHelper $apiHelper)
    {
        $githubUsername = $request->get('github_username');

        try {
            $organization = $apiHelper->getOrganizationInfo($githubUsername);
        } catch (ClientException $exception) {
            // problem
        }

        dd('test');
    }
}