<?php

namespace StoryGen\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for compressing API responses
 */
class CompressionMiddleware implements MiddlewareInterface
{
    /**
     * Compression level (0-9)
     *
     * @var int
     */
    private int $compressionLevel;

    /**
     * Constructor
     *
     * @param int $compressionLevel Compression level (0-9, default: 6)
     */
    public function __construct(int $compressionLevel = 6)
    {
        $this->compressionLevel = $compressionLevel;
    }

    /**
     * Process the request and compress the response if applicable
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     * @return ResponseInterface The response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        // Check if the client accepts compression
        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');

        // If the client doesn't accept compression, return the response as is
        if (empty($acceptEncoding)) {
            return $response;
        }

        // Get the response body
        $body = (string) $response->getBody();

        // If the response body is empty, return the response as is
        if (empty($body)) {
            return $response;
        }

        // Determine the compression method based on what the client accepts
        $compressionMethod = null;
        $encodedBody = null;

        if (strpos($acceptEncoding, 'gzip') !== false) {
            $compressionMethod = 'gzip';
            $encodedBody = gzencode($body, $this->compressionLevel);
        } elseif (strpos($acceptEncoding, 'deflate') !== false) {
            $compressionMethod = 'deflate';
            $encodedBody = gzdeflate($body, $this->compressionLevel);
        }

        // If compression was applied, update the response
        if ($compressionMethod !== null && $encodedBody !== false) {
            // Create a new stream with the compressed content
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $encodedBody);
            rewind($stream);

            // Create a new response with the compressed body
            $response = $response
                ->withBody(new \Slim\Psr7\Stream($stream))
                ->withHeader('Content-Encoding', $compressionMethod)
                ->withHeader('Content-Length', (string)strlen($encodedBody));

            return $response;
        }

        // If compression wasn't applied, return the original response
        return $response;
    }
}
