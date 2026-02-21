protected $routeMiddleware = [
    // Outros middlewares...
    'isAdmin' => \App\Http\Middleware\IsAdminMiddleware::class,
];
