<?php

namespace App\GitHub;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubApiHelper
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getGithubInfo(string $inputUserName): array
    {

        $user = $this->httpClient->request('GET', 'https://api.github.com/users/' . $inputUserName);
        $userRepos = $this->httpClient->request('GET', 'https://api.github.com/users/' . $inputUserName  . '/repos');

        $user = $user->toArray();
        $userRepos = $userRepos->toArray();

        // Username and a link to the users website (if any is provided)
        $userData['name'] = $user['name'] ?? '';
        $userData['blog'] = $user['blog'] ?? '';
        $userData['avatar_url'] = $user['avatar_url'] ?? '';
        $userData['email'] = $user['email'] ?? '';
        $userData['location'] = $user['location'] ?? '';
        $userData['company'] = $user['company'] ?? '';

        $repoData = $languagePercentages = array();
        // Amount and list of repositories (name, link and description)
        $numberOfRepositories = count($userRepos);
        if ( isset($userRepos) && is_array($userRepos) )
        {
            $count = 0;
            foreach ($userRepos as $key => $userRepo)
            {
                $repoData[$key]['name'] = $userRepo['name'];
                $repoData[$key]['html_url'] = $userRepo['html_url'] ?? '/';
                $repoData[$key]['description'] = $userRepo['description'] ?? '/';
                $timeCreated = strtotime($userRepo['created_at']);
                $repoData[$key]['created_at'] = $userRepo['created_at'] ? date("j, n, Y", $timeCreated) : 'N/A';

                if (isset($userRepo['language']))
                {
                    $count++;
                    $programmingLanguages[$userRepo['language']][] = $userRepo['language'];
                }
                // Percentages of programming languages for the account (Aggregated by primary
                // language of the repository in ratio to the size of the repository
            }
        }

        if (isset($programmingLanguages) && is_array($programmingLanguages))
        {
            foreach ($programmingLanguages as $key => $programmingLanguage)
            {
                $languagePercentages[$key] = round( ((count($programmingLanguage) / $count) * 100), 2);
            }

            rsort($languagePercentages, true);
        }

        return array(
            'language_percentages' => $languagePercentages,
            'repo_data' => $repoData,
            'user_data' => $userData
        );
    }

}