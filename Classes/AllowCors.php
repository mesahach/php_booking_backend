<?php
namespace MyApp;

class AllowCors
{
    private const ALLOW_CORS_ORIGIN_KEY = 'Access-Control-Allow-Origin';
    private const ALLOW_CORS_METHODS_KEY = 'Access-Control-Allow-Methods';
    private const ALLOW_CORS_HEADER_KEY = 'Access-Control-Allow-Headers';
    private const ALLOW_CORS_CREDENTIAL_KEY = 'Access-Control-Allow-Credentials';
    private const ALLOW_CORS_CONTENT_TYPE_KEY = 'Content-Type';

    private const ALLOW_CORS_METHODS_VALUE = 'GET, POST, PUT, DELETE, OPTIONS';
    private const ALLOW_CORS_HEADER_VALUE = 'Authorization, authorization, Content-Type, X-Request-Origin, x-request-origin';
    private const ALLOW_CORS_CREDENTIAL_VALUE = 'true';
    private const ALLOW_CORS_CONTENT_TYPE_VALUE = 'application/json; charset=UTF-8';

    private string $origin;

    public function __construct(string $origin)
    {
        $this->origin = $origin;
        $this->init();
    }

    /**
     * Initialize CORS headers
     * Initialize the headers for Cross-Origin Resource Sharing (CORS)
     */
    public function init(): void
    {
        $this->set(self::ALLOW_CORS_ORIGIN_KEY, $this->origin);
        $this->set(self::ALLOW_CORS_METHODS_KEY, self::ALLOW_CORS_METHODS_VALUE);
        $this->set(self::ALLOW_CORS_HEADER_KEY, self::ALLOW_CORS_HEADER_VALUE);
        $this->set(self::ALLOW_CORS_CREDENTIAL_KEY, self::ALLOW_CORS_CREDENTIAL_VALUE);
        $this->set(self::ALLOW_CORS_CONTENT_TYPE_KEY, self::ALLOW_CORS_CONTENT_TYPE_VALUE);
    }

    public function set(string $key, string $value): void
    {
        header("$key: $value");
    }
}
