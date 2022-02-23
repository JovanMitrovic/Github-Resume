<?php

namespace App\GitHub;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubApiHelper
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getOrganizationInfo(string $inputUserName): array
    {

        $userData = $this->httpClient->request('GET', 'https://api.github.com/users/' . $inputUserName);
        $userRepos = $this->httpClient->request('GET', 'https://api.github.com/users/' . $inputUserName  . '/repos');

        $userData = $userData->toArray();
        $userRepos = $userRepos->toArray();
dd($userData);
        // Username and a link to the users website (if any is provided)
        $userName = $userData['login'];
        $usersWebSite = $userData['blog'];

        $repoData = array();
        // Amount and list of repositories (name, link and description)
        if ( isset($userRepos) && is_array($userRepos) )
        {
            foreach ($userRepos as $key => $userRepo)
            {
                $repoData[$key]['name'] = $userRepo['name'];
                $repoData[$key]['html_url'] = $userRepo['html_url'] ?? '/';
                $repoData[$key]['description'] = $userRepo['description'] ?? '/';
dd($userRepo);
                // Percentages of programming languages for the account (Aggregated by primary
                // language of the repository in ratio to the size of the repository
//                dd($userRepo['name']);
            }
        }
        dd($repoData);
        return $repoData;
    }

    /**
     * @return GitHubRepository[]
     */
    public function getOrganizationRepositories(string $organization): array
    {
        $response = $this->httpClient->request('GET', sprintf('https://api.github.com/orgs/%s/repos', $organization));

        $data = $response->toArray();

        $repositories = [];
        foreach ($data as $repoData) {
            $repositories[] = new GitHubRepository(
                $repoData['name'],
                $repoData['html_url'],
                \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $repoData['updated_at'])
            );
        }

        return $repositories;
    }
}