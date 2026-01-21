<?php
// config.php - Configuration and helper functions

// Simple router class
class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            // Convert route path with parameters to regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestPath, $matches)) {
                array_shift($matches); // Remove full match
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }
        
        // No route found
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}

// Database connection (imaginary)
function getDBConnection() {
    // In real implementation, this would connect to actual database
    return [
        'host' => 'localhost',
        'dbname' => 'myapp',
        'user' => 'admin',
        'password' => 'secure_password'
    ];
}

// Authentication middleware (imaginary)
function requireAuth() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    return $_SESSION['user_id'];
}

// Response helper
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
