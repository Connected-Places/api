<?php

namespace App\CiviCrm;

use App\Models\Organisation;
use App\Transformers\CiviCrm\OrganisationTransformer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class CiviClient implements ClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var \App\Transformers\CiviCrm\OrganisationTransformer
     */
    protected $transformer;

    /**
     * CiviClient constructor.
     *
     * @param \GuzzleHttp\Client $httpClient
     * @param string $domain
     * @param string $siteKey
     * @param string $apiKey
     * @param \App\Transformers\CiviCrm\OrganisationTransformer $transformer
     */
    public function __construct(
        Client $httpClient,
        string $domain,
        string $siteKey,
        string $apiKey,
        OrganisationTransformer $transformer
    ) {
        $this->httpClient = $httpClient;
        $this->domain = $domain;
        $this->siteKey = $siteKey;
        $this->apiKey = $apiKey;
        $this->transformer = $transformer;
    }

    /**
     * @inheritDoc
     * @throws \App\CiviCrm\CiviException
     */
    public function create(Organisation $organisation): string
    {
        $response = $this->postRequest(
            $this->transformer->transformCreate($organisation)
        );

        $response = $this->decodeResponse($response);

        return $response['id'];
    }

    /**
     * @inheritDoc
     * @throws \App\CiviCrm\CiviException
     */
    public function update(Organisation $organisation): void
    {
        $this->postRequest(
            $this->transformer->transformUpdate($organisation)
        );
    }

    /**
     * @inheritDoc
     * @throws \App\CiviCrm\CiviException
     */
    public function delete(Organisation $organisation): void
    {
        $this->postRequest(
            $this->transformer->transformDelete($organisation)
        );
    }

    /**
     * @return string
     */
    protected function getEndpoint(): string
    {
        return "{$this->domain}/sites/all/modules/civicrm/extern/rest.php";
    }

    /**
     * @param array $params
     * @throws \App\CiviCrm\CiviException
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function postRequest(array $params): ResponseInterface
    {
        try {
            $response = $this->httpClient->post($this->getEndpoint(), [
                'query' => $this->transformParams($params),
            ]);
        } catch (ClientException $exception) {
            throw new CiviException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $data = $this->decodeResponse($response);

        if ($data['is_error'] ?? 0 === 1) {
            throw new CiviException($data['error_message'], $data['error_code']);
        }

        return $response;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function transformParams(array $params): array
    {
        return [
            'key' => $this->siteKey,
            'api_key' => $this->apiKey,
            'entity' => 'Contact',
            'action' => 'create',
            'json' => json_encode($params),
        ];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $response->getBody()->rewind();

        return json_decode($contents, true);
    }
}