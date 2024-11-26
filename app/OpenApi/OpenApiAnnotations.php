<?php

namespace App\OpenApi;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="News Aggregator System API",
 *         description="API Documentation for News Aggregator Application"
 *     ),
 *     @OA\Server(
 *         description="Local development server",
 *         url="http://localhost:8000"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Laravel Sanctum"
 * )
 */
class OpenApiAnnotations
{
    // This is an empty class used only for global OpenAPI annotations
}