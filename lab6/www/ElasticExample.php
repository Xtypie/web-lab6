<?php
namespace App;
use App\Helpers\ClientFactory;

class ElasticExample
{
    private $client;

    public function __construct()
    {
        $this->client = ClientFactory::make('http://elasticsearch:9200/');
    }

    public function indexDocument($index, $id, $data)
    {
        $response = $this->client->put("$index/_doc/$id", [
            'json' => $data
        ]);
        return $response->getBody()->getContents();
    }

    public function search($index, $query)
    {
        $response = $this->client->get("$index/_search", [
            'json' => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['title', 'content', 'author']
                    ]
                ]
            ]
        ]);
        return $response->getBody()->getContents();
    }

    public function deleteIndex($index)
    {
        try {
            $this->client->delete($index);
        } catch (\Exception $e) {}
    }
}