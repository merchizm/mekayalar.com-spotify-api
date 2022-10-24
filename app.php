<?php

namespace Rocks;

use Dotenv\Dotenv;
use Exception;
use SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use SpotifyWebAPI\SpotifyWebAPIException;

class app
{
    /**
     * @var SpotifyWebAPI\Session
     */
    private $session;
    /**
     * @var string[][]
     */
    private $options;
    /**
     * @var SpotifyWebAPI\SpotifyWebAPI
     */
    private $api;
    /**
     * @var string
     */
    private $json_file_location = __DIR__ . "/cursor.json";
    /**
     * @var mixed
     */
    private $json_data;

    public function __construct()
    {
        // initialize classes
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // initialize spotify API
        $this->session = new SpotifyWebAPI\Session(
            $_ENV['CLIENT_ID'],
            $_ENV['CLIENT_SECRET'],
            $_ENV['CALLBACK_URL']
        );

        $this->options = [
            'scope' => [
                'user-read-currently-playing',
            ],
        ];

        $this->api = new SpotifyWebAPI\SpotifyWebAPI($this->session);

        // load cursor.json
        if (file_exists($this->json_file_location)):
            $this->json_data = json_decode(file_get_contents($this->json_file_location), true);
        else:
            die(json_encode(['error' => 'The cursor file could not be loaded.']));
        endif;
    }

    /**
     * parse url parameters
     * @return array
     */
    private function parseUrlParameters(): array
    {
        return (isset($_GET['page'])) ? explode('/', filter_var(rtrim($_GET['page'], '/'), FILTER_SANITIZE_URL)) : [];
    }


    // TODO: FIX THIS FUNCTION
    /**
     * run app
     * @return false|string
     */
    public function run()
    {
        $url = $this->parseUrlParameters();
        $route = (isset($url[key($url)]) ? htmlspecialchars(current($url), ENT_QUOTES, 'UTF-8') : 'index');

        switch (strtolower($route)) {
            case 'auth':
                header('Location: ' . $this->session->getAuthorizeUrl($this->options)); // redirect to spotify auth page
                return false;
            case 'json':
                try {
                    // check result is valid
                    $result = json_decode($this->currentPlaying(), true);

                    $last_response = $this->api->getLastResponse();
                    if($result['is_playing'] === false || $last_response['status'] ==! 200 || $last_response['status'] ==! 204){ // @see https://developer.spotify.com/documentation/web-api/
                        $this->refreshToken();
                        return $this->currentPlaying();
                    }else
                        return json_encode($result);
                } catch (SpotifyWebAPIAuthException $ex) {
                    return json_encode(['error' => 'The access token could not be refreshed.', 'is_playing' => false]);
                } catch (SpotifyWebAPIException $ex) { // if access token is expired, renew with refresh token and try again
                    $this->refreshToken();
                    return $this->currentPlaying();
                }
            default:
                return json_encode(['status' => '🌤', 'message' => 'successfully running']);
        }
    }

    /**
     * refresh the access token
     * @return void
     */
    private function refreshToken()
    {
        $this->session->refreshAccessToken($this->json_data['refresh_token']);
        $this->json_data['access_token'] = $this->session->getAccessToken();
        $this->json_data['refresh_token'] = $this->session->getRefreshToken();
        file_put_contents($this->json_file_location, json_encode($this->json_data));
    }

    /**
     * get current playing song
     * @return false|string
     */
    private function currentPlaying()
    {
        $this->api->setAccessToken($this->json_data['access_token']);
        $result = (array)$this->api->getMyCurrentTrack();
        if(empty($result))
            return json_encode(['is_playing' => false]);
        return json_encode(["name" => $result['item']->name, "artists" => $result['item']->artists, "is_playing" => $result["is_playing"], "url" => $result['item']->external_urls->spotify]);
    }

    public function __destruct()
    {
        try {
            if (strlen(json_encode($this->json_data)) > 4)
                file_put_contents($this->json_file_location, json_encode($this->json_data));
        } catch (Exception $ex) {
            die(json_encode(['error' => 'The cursor file could not be saved.']));
        }
    }
}

