<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class GenerateApiDocs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:docs {--output=docs/API_DOCUMENTATION.md}';

    /**
     * The console command description.
     */
    protected $description = 'Generate comprehensive API documentation from routes and actions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $outputPath = $this->option('output');
        $routes = $this->getApiRoutes();
        $documentation = $this->generateDocumentation($routes);
        
        file_put_contents($outputPath, $documentation);
        
        $this->info("API documentation generated at: {$outputPath}");
        
        return Command::SUCCESS;
    }

    /**
     * Get all API routes.
     */
    private function getApiRoutes(): array
    {
        $routes = [];
        
        foreach (Route::getRoutes() as $route) {
            if (str_starts_with($route->uri(), 'api/v1/')) {
                $routes[] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->gatherMiddleware(),
                ];
            }
        }
        
        return $routes;
    }

    /**
     * Generate documentation from routes.
     */
    private function generateDocumentation(array $routes): string
    {
        $doc = "# Kink Master API Documentation\n\n";
        $doc .= "## Overview\n\n";
        $doc .= "The Kink Master API provides comprehensive access to all platform features for mobile applications. ";
        $doc .= "The API follows RESTful conventions and uses JSON for data exchange.\n\n";
        $doc .= "**Base URL:** `https://your-domain.com/api/v1`\n\n";
        
        $doc .= "## Authentication\n\n";
        $doc .= "The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:\n\n";
        $doc .= "```\nAuthorization: Bearer {your-token}\n```\n\n";
        
        $doc .= "## Response Format\n\n";
        $doc .= "All API responses follow this structure:\n\n";
        $doc .= "```json\n";
        $doc .= "{\n";
        $doc .= "  \"success\": true,\n";
        $doc .= "  \"message\": \"Optional message\",\n";
        $doc .= "  \"data\": { ... }\n";
        $doc .= "}\n";
        $doc .= "```\n\n";
        
        $doc .= "Error responses:\n\n";
        $doc .= "```json\n";
        $doc .= "{\n";
        $doc .= "  \"success\": false,\n";
        $doc .= "  \"message\": \"Error description\",\n";
        $doc .= "  \"errors\": { ... }\n";
        $doc .= "}\n";
        $doc .= "```\n\n";
        
        $doc .= "## Endpoints\n\n";
        
        // Group routes by prefix
        $groupedRoutes = $this->groupRoutesByPrefix($routes);
        
        foreach ($groupedRoutes as $prefix => $groupRoutes) {
            $doc .= "### " . ucfirst(str_replace(['api/v1/', '/'], ['', ' '], $prefix)) . "\n\n";
            
            foreach ($groupRoutes as $route) {
                $doc .= $this->generateRouteDocumentation($route);
            }
        }
        
        $doc .= "## Error Codes\n\n";
        $doc .= "- `400` - Bad Request (validation errors)\n";
        $doc .= "- `401` - Unauthorized (invalid or missing token)\n";
        $doc .= "- `403` - Forbidden (insufficient permissions)\n";
        $doc .= "- `404` - Not Found\n";
        $doc .= "- `422` - Unprocessable Entity (validation failed)\n";
        $doc .= "- `500` - Internal Server Error\n\n";
        
        $doc .= "## Rate Limiting\n\n";
        $doc .= "API requests are rate limited to prevent abuse. The limits are:\n\n";
        $doc .= "- **Authentication endpoints:** 5 requests per minute\n";
        $doc .= "- **Content creation:** 10 requests per minute\n";
        $doc .= "- **General API:** 60 requests per minute\n\n";
        
        $doc .= "Rate limit headers are included in responses:\n";
        $doc .= "- `X-RateLimit-Limit`\n";
        $doc .= "- `X-RateLimit-Remaining`\n";
        $doc .= "- `X-RateLimit-Reset`\n\n";
        
        return $doc;
    }

    /**
     * Group routes by prefix.
     */
    private function groupRoutesByPrefix(array $routes): array
    {
        $grouped = [];
        
        foreach ($routes as $route) {
            $uri = $route['uri'];
            $segments = explode('/', $uri);
            
            if (count($segments) >= 3) {
                $prefix = $segments[0] . '/' . $segments[1] . '/' . $segments[2];
                $grouped[$prefix][] = $route;
            }
        }
        
        return $grouped;
    }

    /**
     * Generate documentation for a single route.
     */
    private function generateRouteDocumentation(array $route): string
    {
        $method = $route['method'];
        $uri = $route['uri'];
        $name = $route['name'];
        
        $doc = "#### " . strtoupper($method) . " `/{$uri}`\n\n";
        
        if ($name) {
            $doc .= "**Route Name:** `{$name}`\n\n";
        }
        
        // Add authentication requirement
        if (in_array('auth:sanctum', $route['middleware'])) {
            $doc .= "**Authentication:** Required\n\n";
            $doc .= "**Headers:** `Authorization: Bearer {token}`\n\n";
        } else {
            $doc .= "**Authentication:** Not required\n\n";
        }
        
        // Add description based on route
        $doc .= $this->getRouteDescription($uri, $method) . "\n\n";
        
        // Add request/response examples
        $doc .= $this->getRouteExamples($uri, $method) . "\n\n";
        
        return $doc;
    }

    /**
     * Get route description.
     */
    private function getRouteDescription(string $uri, string $method): string
    {
        $descriptions = [
            'auth/register' => 'Register a new user account.',
            'auth/login' => 'Authenticate a user and return an access token.',
            'auth/logout' => 'Revoke the current access token.',
            'auth/user' => 'Get the authenticated user\'s profile information.',
            'user/profile' => 'Update the authenticated user\'s profile.',
            'tasks' => 'Get the authenticated user\'s assigned tasks.',
            'tasks/active' => 'Get the user\'s currently active task.',
            'tasks/complete' => 'Complete the user\'s active task.',
            'tasks/stats' => 'Get the user\'s task statistics and streaks.',
            'content/stories' => 'Get published stories.',
            'content/stories/{slug}' => 'Get a specific story by slug.',
            'content/statuses' => 'Get public status updates.',
            'content/fantasies' => 'Get published fantasies.',
            'subscription/plans' => 'Get all available subscription plans.',
            'subscription/current' => 'Get the authenticated user\'s current subscription.',
            'subscription/checkout' => 'Create a Stripe checkout session for subscription.',
            'subscription/cancel' => 'Cancel the user\'s current subscription.',
            'subscription/billing-portal' => 'Get the Stripe billing portal URL for subscription management.',
            'reactions/toggle' => 'Toggle a reaction on content.',
            'search' => 'Search across all content types.',
        ];
        
        $baseUri = str_replace(['{slug}', '{id}'], ['{slug}', '{id}'], $uri);
        return $descriptions[$baseUri] ?? 'API endpoint for ' . str_replace('/', ' ', $baseUri) . '.';
    }

    /**
     * Get route examples.
     */
    private function getRouteExamples(string $uri, string $method): string
    {
        $examples = [
            'auth/register' => [
                'request' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                    'user_type' => 1,
                ],
                'response' => [
                    'success' => true,
                    'message' => 'User registered successfully',
                    'user' => ['id' => 1, 'name' => 'John Doe'],
                    'token' => '1|abc123...',
                ],
            ],
            'auth/login' => [
                'request' => [
                    'email' => 'john@example.com',
                    'password' => 'password123',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => ['id' => 1, 'name' => 'John Doe'],
                    'token' => '1|abc123...',
                ],
            ],
        ];
        
        $baseUri = str_replace(['{slug}', '{id}'], ['{slug}', '{id}'], $uri);
        
        if (isset($examples[$baseUri])) {
            $example = $examples[$baseUri];
            $doc = "**Request Body:**\n```json\n" . json_encode($example['request'], JSON_PRETTY_PRINT) . "\n```\n\n";
            $doc .= "**Response:**\n```json\n" . json_encode($example['response'], JSON_PRETTY_PRINT) . "\n```\n";
            return $doc;
        }
        
        return "**Response:** Standard API response format.";
    }
}