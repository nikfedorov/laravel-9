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
        // try to get emails
        try {

            // request elasticsearch
            $data = Elasticsearch::search([
                'index' => $this->prefix.'emails',
            ]);

            // collect and map result into an array
            return collect($data['hits']['hits'])
                ->map(function ($item) {
                    return [
                        'email' => $item['_source']['email'],
                        'subject' => $item['_source']['subject'],
                        'body' => $item['_source']['body'],
                    ];
                })
                ->toArray();
        }

        // on error return empty array
        catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Delete an index.
     */
    public function deleteAll()
    {
        try {
            Elasticsearch::indices()->delete([
                'index' => $this->prefix.'emails',
            ]);
        } catch (\Exception $e) {
        }
    }
}
