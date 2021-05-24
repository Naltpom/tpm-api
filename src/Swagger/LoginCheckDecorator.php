<?php

declare(strict_types=1);

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class LoginCheckDecorator.
 */
class LoginCheckDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * SwaggerDecorator constructor.
     */
    public function __construct(NormalizerInterface $decorated)
    {
        $this->normalizer = $decorated;
    }

    /**
     * @param mixed       $object
     * @param string|null $format
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->normalizer->normalize($object, $format, $context);

        $docs['paths']['/login_check'] = [
            'post' => [
                'tags' => ['Jwt Token'],
                'produces' => ['application/json'],
                'consumes' => ['application/x-www-form-urlencoded'],
                'operationId' => 'getAccessToken',
                'summary' => 'Get Access Token',
                'parameters' => $this->getParameters(),
                'responses' => $this->getResponse(),
            ],
        ];

        return $docs;
    }

    /**
     * @param mixed       $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }

    private function getParameters(): array
    {
        return [
            [
                'in' => 'formData',
                'name' => 'username',
                'description' => '',
                'required' => true,
                'type' => 'string',
            ],
            [
                'in' => 'formData',
                'name' => 'password',
                'description' => '',
                'required' => true,
                'type' => 'string',
            ],
        ];
    }

    private function getResponse(): array
    {
        return [
            200 => [
                'description' => 'Success',
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'token' => ['type' => 'string'],
                    ],
                ],
            ],
            'default' => [
                'description' => 'An Error have been throw',
                'schema' => [
                    '$ref' => '#/definitions/Error',
                ],
            ],
        ];
    }
}
