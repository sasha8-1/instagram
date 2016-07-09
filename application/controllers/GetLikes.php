<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetLikes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->config('instafloor');
        $this->load->library('phpquery/phpquery');
        $this->load->helper('request');

        $this->client = new GuzzleHttp\Client();

    }

    private function getInstaFloorTask() {
        $instafloorPage = $this->client->request('POST', $this->config->item('url'), [
            'headers' => [
                'user-agent' => $this->config->item('agent'),
                'cookie'     => 'hash='.$this->config->item('cookie')['hash'].'; id='.$this->config->item('cookie')['id'].'; r=1'
            ]
        ]);

        return $instafloorPage->getBody();
    }

    private function getInstagramUserPage($userLikeUrl) {
        $instagramUserPage = $this->client->request('GET', $userLikeUrl, [
            'headers' => [
                'user-agent' => $this->config->item('agent'),
            ],
            'curl' => [
                CURLOPT_COOKIEFILE => $this->config->item('PATH_COOKIE').'cookies.txt',
            ],
        ]);
        return $instagramUserPage->getBody();
    }

    private function setLike($photoId, $userLikeUrl) {
        $addLike = $this->client->request('POST', 'https://www.instagram.com/web/likes/'.$photoId.'/like/', [
            'headers' => [
                'user-agent' => $this->config->item('agent'),

                'referer' => $userLikeUrl,
                'origin' => 'https://www.instagram.com',
                'x-csrftoken' => '926521fb8659c5fd9029b425d80e3290',
                'accept' => '*/*',
                'accept-encoding' => 'gzip, deflate',
                'accept-language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4,ro;q=0.2',
                'cache-control' => 'no-cache',
                'content-length' => '0',
                'pragma' => 'no-cache',
                'x-instagram-ajax' => '1',
                'x-requested-with' => 'XMLHttpRequest',
                'cookie' => 'mid=VwU7gQAEAAEg6qfygegYkH7pE5mN; fbm_124024574287414=base_domain=.instagram.com; sessionid=IGSC6240c034b6ac07d2ca3cb2574e692ab29d52bde177b77ec777f99c9cf0e6b8a4%3AqB24YOi3d7c5rTRsvovIzoKEB1xGnoE2%3A%7B%22_token_ver%22%3A2%2C%22_auth_user_id%22%3A403778782%2C%22_token%22%3A%22403778782%3A9z5PGnw9MZF54PfKjFhcxlp2o09PdzlB%3A8da2e0076e086b70ae69e89a886c9843e9751ae2dec4ec2cb330b19d9ee60846%22%2C%22asns%22%3A%7B%22188.138.149.40%22%3A31252%2C%22time%22%3A1466104898%7D%2C%22_auth_user_backend%22%3A%22accounts.backends.CaseInsensitiveModelBackend%22%2C%22last_refreshed%22%3A1466104899.016189%2C%22_platform%22%3A4%7D; s_network=; ig_pr=1; ig_vw=409; csrftoken=926521fb8659c5fd9029b425d80e3290; ds_user_id=403778782; mid=VwU7gQAEAAEg6qfygegYkH7pE5mN; fbm_124024574287414=base_domain=.instagram.com; sessionid=IGSC70ab6f8d541167dd2c3b9bce38568e5f5f3922abb8c301e5c14142a291fd250d%3AIrZpvw94Ja8qi7zrh10rgVhYX6SPLZ3g%3A%7B%22_token_ver%22%3A2%2C%22_auth_user_id%22%3A403778782%2C%22_token%22%3A%22403778782%3A9z5PGnw9MZF54PfKjFhcxlp2o09PdzlB%3A8da2e0076e086b70ae69e89a886c9843e9751ae2dec4ec2cb330b19d9ee60846%22%2C%22asns%22%3A%7B%22188.138.149.40%22%3A31252%2C%22time%22%3A1466194313%7D%2C%22_auth_user_backend%22%3A%22accounts.backends.CaseInsensitiveModelBackend%22%2C%22last_refreshed%22%3A1466193593.38435%2C%22_platform%22%3A4%7D; s_network=; ig_pr=1; ig_vw=1227; csrftoken=926521fb8659c5fd9029b425d80e3290; ds_user_id=403778782'
            ],
            'curl' => [
                CURLOPT_COOKIEFILE => $this->config->item('PATH_COOKIE').'cookies.txt',

            ],
        ]);
        return $addLike->getBody();
    }

    private function getMoney($idTask) {
        $client = new GuzzleHttp\Client();
        $post = [
            'ajax' => 1,
            'id'   => $idTask,
            'page' => 'check',
            'rand' => rand(1, 99999999999)
        ];
        $money = $client->post($this->config->item('url').'index.php?act=ajax', [
            'headers' => [
                'user-agent' => $this->config->item('agent'),
                'cookie'     => 'hash='.$this->config->item('cookie')['hash'].'; id='.$this->config->item('cookie')['id'].'; r=1; g=1466098105',
                'X-Requested-With' => 'XMLHttpRequest'
            ],
            'form_params' => $post,
        ]);

        return $money->getBody();
    }

    public function index()
    {

        // Get new task on instafloor
        $instafloorPage = $this->getInstaFloorTask();
        preg_match("/.+?(<div.*<\/div>)/s", $instafloorPage, $matches);
        $document = $this->phpquery->newDocumentHTML($instafloorPage);
        $userLikeUrl = pq($document->find('a#go_clickok'))->attr("href");

        $userTask = pq($document->find('a#clickok'))->attr("href");
        preg_match("/id=(\d*)$/", $userTask, $matches);
        if (!$matches) exit('Tasks is empty.Please try again later');

        $idTask = $matches[1];

        echo 'Instagram user url - '.$userLikeUrl.'<br />';

//        sleep(rand(3,7));

        // go on user url and get page
        $instagramUserPage = $this->getInstagramUserPage($userLikeUrl);
        preg_match("/window._sharedData = (.+?);<\/script>/s", $instagramUserPage, $matches);
        $photoId = json_decode($matches[1])->entry_data->PostPage[0]->media->id;

        // add like to user
        $addLike = $this->setLike($photoId, $userLikeUrl);

        $objectAddLike = json_decode($addLike);
        if (!property_exists($objectAddLike, 'status') || $objectAddLike->status !== 'ok') {
            exit('When we add like instagram return false');
        }

//        sleep(rand(5, 14));

        // get money
        $money = $this->getMoney($idTask);
        $responce = json_decode($money);
        if ($responce->status == 'ok') {
            echo 'yes';
        } else {
            echo 'error='.$responce->error;
        }

//        echo $money;

    }

}

