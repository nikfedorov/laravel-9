<?php

namespace App\Services;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Elasticsearch;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    /**
     * Prefix for search indexes.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Instantiate Service.
     */
    public function __construct()
    {
        $this->prefix = config('elasticsearch.prefix');
    }

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  string  $messageBody
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */
    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        $data = [
            'body' => [
                'email' => $toEmailAddress,
                'subject' => $messageSubject,
                'body' => $messageBody,
            ],
            'index' => $this->prefix.'emails',
        ];

        return Elasticsearch::index($data);
    }

    /**
     * Get a list of sent emails.
     */
    public function getEmails()
    {
        $params = [
            'index' => $this->prefix.'emails',
        ];

        return Elasticsearch::search($params);
    }

    /**
     * Delete an index.
     */
    public function deleteAll()
    {
        $params = [
            'index' => $this->prefix.'emails',
        ];

        return Elasticsearch::indices()->delete($params);
    }
}
