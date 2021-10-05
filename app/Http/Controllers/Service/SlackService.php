<?php

namespace App\Http\Controllers\Service;

use GuzzleHttp\Client;

class SlackService
{
    protected $httpClient;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    public function ListChannels($types)
    {
        return $this->get("conversations.list", ["types" => $types], "channels");
    }

    public function get($name, $parameters = null, $content_key = null)
    {
        $data = array();
        do {
            $response = $this->http($name, $parameters);

            $cursor = $response->response_metadata->next_cursor ?? null;
            $data[] = $response->$content_key;
        } while ($cursor != null);

        return call_user_func_array('array_merge', $data);

        return $data;
    }

    public function http($query, $parameters = [])
    {
        $parameters["limit"] = 1000;
        $parameters["token"] = auth()->user()->token;
        $query .= "?";
        foreach ($parameters as $key => $item) {
            $query .= $key . "=" . $item . "&";
        }
        $endpoint = "https://slack.com/api/$query";

        $response = $this->httpClient->request('GET', $endpoint);

        return json_decode($response->getBody()->getContents());
    }

    public function listUser()
    {
        return $this->get("users.list", [], "members");
    }

    public function retrieveMessages($channelId)
    {
        return $this->get("conversations.history", ["channel" => $channelId], "messages");
    }

    public function retrieveChannelMembers($channelId)
    {
        return $this->get("conversations.members", ["channel" => $channelId], "members");
    }
}
