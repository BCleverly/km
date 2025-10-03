<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process JSON responses
        if (!$request->expectsJson() && !$request->is('api/*')) {
            return $response;
        }

        // Add common headers for API responses
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('X-API-Version', '1.0');
        $response->headers->set('X-Request-ID', $request->header('X-Request-ID', uniqid()));

        // Add rate limiting headers if available
        if ($response->headers->has('X-RateLimit-Limit')) {
            $response->headers->set('X-RateLimit-Limit', $response->headers->get('X-RateLimit-Limit'));
            $response->headers->set('X-RateLimit-Remaining', $response->headers->get('X-RateLimit-Remaining'));
            $response->headers->set('X-RateLimit-Reset', $response->headers->get('X-RateLimit-Reset'));
        }

        // Handle different response types
        if ($response instanceof Response) {
            $this->handleLaravelResponse($response, $request);
        }

        return $response;
    }

    /**
     * Handle Laravel Response objects.
     */
    private function handleLaravelResponse(Response $response, Request $request): void
    {
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        // Handle empty responses
        if (empty($content) && $statusCode === 200) {
            $response->setContent(json_encode([
                'success' => true,
                'message' => 'Success',
            ]));
        }

        // Handle validation errors
        if ($statusCode === 422) {
            $this->formatValidationErrors($response);
        }

        // Handle authentication errors
        if ($statusCode === 401) {
            $this->formatAuthenticationError($response);
        }

        // Handle authorization errors
        if ($statusCode === 403) {
            $this->formatAuthorizationError($response);
        }

        // Handle not found errors
        if ($statusCode === 404) {
            $this->formatNotFoundError($response);
        }

        // Handle server errors
        if ($statusCode >= 500) {
            $this->formatServerError($response);
        }
    }

    /**
     * Format validation errors.
     */
    private function formatValidationErrors(Response $response): void
    {
        $content = json_decode($response->getContent(), true);
        
        if (isset($content['errors'])) {
            $formattedContent = [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $content['errors'],
            ];
            
            $response->setContent(json_encode($formattedContent));
        }
    }

    /**
     * Format authentication errors.
     */
    private function formatAuthenticationError(Response $response): void
    {
        $content = json_decode($response->getContent(), true);
        
        $formattedContent = [
            'success' => false,
            'message' => $content['message'] ?? 'Authentication required',
        ];
        
        $response->setContent(json_encode($formattedContent));
    }

    /**
     * Format authorization errors.
     */
    private function formatAuthorizationError(Response $response): void
    {
        $content = json_decode($response->getContent(), true);
        
        $formattedContent = [
            'success' => false,
            'message' => $content['message'] ?? 'Insufficient permissions',
        ];
        
        $response->setContent(json_encode($formattedContent));
    }

    /**
     * Format not found errors.
     */
    private function formatNotFoundError(Response $response): void
    {
        $content = json_decode($response->getContent(), true);
        
        $formattedContent = [
            'success' => false,
            'message' => $content['message'] ?? 'Resource not found',
        ];
        
        $response->setContent(json_encode($formattedContent));
    }

    /**
     * Format server errors.
     */
    private function formatServerError(Response $response): void
    {
        $content = json_decode($response->getContent(), true);
        
        $formattedContent = [
            'success' => false,
            'message' => config('app.debug') 
                ? ($content['message'] ?? 'Internal server error')
                : 'Internal server error',
        ];
        
        if (config('app.debug') && isset($content['exception'])) {
            $formattedContent['exception'] = $content['exception'];
        }
        
        $response->setContent(json_encode($formattedContent));
    }
}