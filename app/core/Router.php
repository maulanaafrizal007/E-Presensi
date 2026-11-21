<?php
/**
 * Router — simple URL dispatcher
 */
class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->routes[] = ['GET', $path, $handler];
    }

    public function post(string $path, string $handler): void
    {
        $this->routes[] = ['POST', $path, $handler];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the base directory prefix (e.g. /E-Presensi)
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base !== '' && $base !== '/' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        if (empty($uri)) $uri = '/';

        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routeMethod !== $method) continue;

            // Convert {id} placeholder to regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // remove full match
                [$class, $action] = explode('@', $handler);
                $controller = new $class();
                $controller->$action(...$matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        $this->render404();
    }

    private function render404(): void
    {
        echo render_layout('404 - Halaman Tidak Ditemukan', '<div class="empty-state" style="margin:4rem auto;max-width:400px;text-align:center;">
            <div style="font-size:4rem;color:#1a3561;font-weight:800;">404</div>
            <h4>Halaman Tidak Ditemukan</h4>
            <p class="text-muted">Halaman yang Anda cari tidak tersedia.</p>
            <a href="' . url('/dashboard') . '" class="btn btn-navy">Kembali ke Dashboard</a>
        </div>');
    }
}
