<?php

namespace Guysolamour\Admin\Console;


use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class AdminInstallCommand extends Command
{
    protected  const TPL_PATH = __DIR__. '/../templates';

    protected  $name = '';
    protected $exits = false;

    protected $override = false;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:install
                                {name=admin : Name of the guard.}
                                {--f|force : Whether to override existing files}';


    protected $description = 'Install admin package';


    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(12);

        $this->name = $this->argument('name');

        $this->override = $this->option('force') ? true : false;

        // Check if the guard is already registered
        if (array_key_exists(Str::singular(Str::snake($this->name)),config('auth.guards'))) {
            $this->exits = true;

            if (!$this->option('force')) {
                $this->info("Guard: '" . $this->name . "' is already registered");
                if (!$this->confirm('Force override resources...?')) {
                    $this->info(PHP_EOL . 'Halting scaffolding, try again with a another guard name...');
                    // throw new \RuntimeException("Halting installation, choose another guard name...");
                }
                // Override resources
                $this->override = true;
            }
        }

        $this->info("Using guard: '" . $this->name . "'");

        $progress->advance();

        // Configurations
        $this->info(PHP_EOL . 'Registering configurations...');

        if ($this->exits && $this->override) {
            $this->info('Configurations registration skipped');
        } else {
            $this->registerConfigurations(self::TPL_PATH);
            $this->info('Configurations registered in ' . config_path('auth.php'));
        }

        $progress->advance();

        // Models

        // Models
        $this->info(PHP_EOL . 'Creating Model...');

        $model_path = $this->loadModel(self::TPL_PATH);

        $this->info('Model created at ' . $model_path);

        $progress->advance();

        // Factories
        $this->info(PHP_EOL . 'Creating Factory...');

        $factory_path = $this->loadFactory(self::TPL_PATH);

        $this->info('Factory created at ' . $factory_path);

        $progress->advance();

        // Notifications
        $this->info(PHP_EOL . 'Creating Notification...');

        $notification_path = $this->loadNotification(self::TPL_PATH);

        $this->info('Notification created at ' . $notification_path);

        $progress->advance();

        // Migrations
        $this->info(PHP_EOL . 'Creating Migrations...');

        if ($this->exits && $this->override) {
            $this->info('Migrations\' creation skipped');
        } else {
            $migrations_path = $this->loadMigrations(self::TPL_PATH);
            $this->info('Migrations created at ' . $migrations_path);
        }

        $progress->advance();

        // Controllers
        $this->info(PHP_EOL . 'Creating Controllers...');

        $controllers_path = $this->loadControllers(self::TPL_PATH);

        $this->info('Controllers created at ' . $controllers_path);

        $progress->advance();


        // Views
        $this->info(PHP_EOL . 'Creating Views...');

        $views_path = $this->loadViews(self::TPL_PATH);

        $this->info('Views created at ' . $views_path);

        $progress->advance();

        // Routes
        $this->info(PHP_EOL . 'Creating Routes...');

        $routes_path = $this->loadRoutes(self::TPL_PATH);

        $this->info('Routes created at ' . $routes_path);

        $progress->advance();

        // Routes Service Provider
        $this->info(PHP_EOL . 'Registering Routes Service Provider...');

        if ($this->exits && $this->override) {
            $this->info('Routes service provider registration skipped');
        } else {
            $routes_sp_path = $this->registerRoutes(self::TPL_PATH);
            $this->info('Routes registered in service provider: ' . $routes_sp_path);
        }

        // Middleware
        $this->info(PHP_EOL . 'Creating Middleware...');

        $middleware_path = $this->loadMiddleware(self::TPL_PATH);

        $this->info('Middleware created at ' . $middleware_path);

        $progress->advance();

        // Route Middleware
        $this->info(PHP_EOL . 'Registering route middleware...');

        if ($this->exits && $this->override) {
            $this->info('Route middleware registration skipped');
        } else {
            $kernel_path = $this->registerRouteMiddleware(self::TPL_PATH);
            $this->info('Route middleware registered in ' . $kernel_path);
        }

        $progress->finish();

    }

    private function registerConfigurations(string $template_path)
    {
        try {

            // get auth file
            $auth = file_get_contents(config_path('auth.php'));
            $data_map = $this->parseName();

            // Guard

            // get template file
            $guards = file_get_contents($template_path . '/config/guards.stub');

            // compile stub...
            $guards = strtr($guards, $data_map);

            $guards_bait = "'guards' => [";

            $auth = str_replace($guards_bait, $guards_bait . $guards, $auth);

            /** Providers */
            $providers = file_get_contents($template_path . '/config/providers.stub');

            // compile stub...
            $providers = strtr($providers, $data_map);


            $providers_bait = "'providers' => [";

            $auth = str_replace($providers_bait, $providers_bait . $providers, $auth);

            /********** Passwords **********/

            $passwords = file_get_contents($template_path . '/config/passwords.stub');

            // compile stub...
            $passwords = strtr($passwords, $data_map);

            $passwords_bait = "'passwords' => [";

            $auth = str_replace($passwords_bait, $passwords_bait . $passwords, $auth);

            // Overwrite config file
            file_put_contents(config_path('auth.php'), $auth);

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }


    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName($name = null)
    {
        if (!$name)
            $name = $this->name;


        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => str_plural(camel_case($name)),
            '{{pluralSlug}}' => str_plural(str_slug($name)),
            '{{pluralSnake}}' => str_plural(snake_case($name)),
            '{{pluralClass}}' => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}' => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
        );
    }

    /**
     * Load model
     * @param $stub_path
     * @return string
     */
    protected function loadModel($stub_path)
    {
        try {

            $stub = file_get_contents($stub_path . '/model.stub');

            $data_map = $this->parseName();

            $model = strtr($stub, $data_map);


            $model_path = app_path($data_map['{{singularClass}}'] . '.php');

            file_put_contents($model_path, $model);

            return $model_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadFactory($template_path)
    {
        try {

            $stub = file_get_contents($template_path . '/factory.stub');


            $data_map = $this->parseName();

            $factory = strtr($stub, $data_map);

            $factory_path = database_path('factories/' . $data_map['{{singularClass}}'] . 'Factory.php');

            file_put_contents($factory_path, $factory);

            return $factory_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadNotification($template_path)
    {
        try {

            $data_map = $this->parseName();

            $notifications_path = app_path('/Notifications/' . $data_map['{{singularClass}}'] . '/Auth');

            $notifications = array(
                [
                    'stub' => $template_path . '/Notifications/ResetPassword.stub',
                    'path' => $notifications_path . '/ResetPassword.php',
                ],
                [
                    'stub' => $template_path . '/Notifications/VerifyEmail.stub',
                    'path' => $notifications_path . '/VerifyEmail.php',
                ],
            );


            foreach ($notifications as $notification) {
                $stub = file_get_contents($notification['stub']);

                $complied = strtr($stub, $data_map);

                $dir = dirname($notification['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }


                file_put_contents($notification['path'], $complied);
            }

            return $notifications_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadMigrations($template_path)
    {
        try {

            $data_map = $this->parseName();


            $signature = date('Y_m_d_His');


            $migrations = array(
                [
                    'stub' => $template_path . '/migrations/provider.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php'),
                ],
                [
                    'stub' => $template_path . '/migrations/password_resets.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{singularSnake}}'] . '_password_resets_table.php'),
                ],
            );

            foreach ($migrations as $migration) {
                $stub = file_get_contents($migration['stub']);
                $complied = strtr($stub, $data_map);

                $dir = dirname($migration['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($migration['path'], $complied);
            }

            return database_path('migrations');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadControllers($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $controllers_path = app_path('/Http/Controllers/' . $guard);

        $controllers = array(
            [
                'stub' => $template_path . '/Controllers/HomeController.stub',
                'path' => $controllers_path . '/HomeController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/Auth/ForgotPasswordController.stub',
                'path' => $controllers_path . '/Auth/ForgotPasswordController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/Auth/LoginController.stub',
                'path' => $controllers_path . '/Auth/LoginController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/Auth/RegisterController.stub',
                'path' => $controllers_path . '/Auth/RegisterController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/Auth/ResetPasswordController.stub',
                'path' => $controllers_path . '/Auth/ResetPasswordController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/Auth/VerificationController.stub',
                'path' => $controllers_path . '/Auth/VerificationController.php',
            ],
        );

        foreach ($controllers as $controller) {
            $stub = file_get_contents($controller['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($controller['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($controller['path'], $complied);
        }

        return $controllers_path;
    }

    protected function loadViews($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $views_path = resource_path('views/' . $guard);

        $views = array(
            [
                'stub' => $template_path . '/views/home.blade.stub',
                'path' => $views_path . '/home.blade.php',
            ],
            [
                'stub' => $template_path . '/views/layouts/app.blade.stub',
                'path' => $views_path . '/layouts/app.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/login.blade.stub',
                'path' => $views_path . '/auth/login.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/register.blade.stub',
                'path' => $views_path . '/auth/register.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/verify.blade.stub',
                'path' => $views_path . '/auth/verify.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/passwords/email.blade.stub',
                'path' => $views_path . '/auth/passwords/email.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/passwords/reset.blade.stub',
                'path' => $views_path . '/auth/passwords/reset.blade.php',
            ],
        );

        foreach ($views as $view) {
            $stub = file_get_contents($view['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($view['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($view['path'], $complied);
        }

        return $views_path;
    }

    protected function loadRoutes($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $routes_path = base_path('/routes/' . $guard . '.php');

        $routes = array(
            'stub' => $template_path . '/routes/routes.stub',
            'path' => $routes_path,
        );

        $stub = file_get_contents($routes['stub']);
        $complied = strtr($stub, $data_map);

        file_put_contents($routes['path'], $complied);

        return $routes_path;
    }

    protected function registerRoutes($template_path)
    {
        try {

            $provider_path = app_path('Providers/RouteServiceProvider.php');

            $provider = file_get_contents($provider_path);

            $data_map = $this->parseName();

            /********** Function **********/

            $stub = $template_path . '/routes/map.stub';

            $map = file_get_contents($stub);

            $map = strtr($map, $data_map);

            $map_bait = "    /**\n" . '     * Define the "web" routes for the application.';

            $provider = str_replace($map_bait, $map . $map_bait, $provider);

            /********** Function Call **********/

            $map_call = file_get_contents($template_path . '/routes/map_call.stub');

            $map_call = strtr($map_call, $data_map);


            $map_call_bait = '$this->mapWebRoutes();';

            $provider = str_replace($map_call_bait, $map_call_bait . $map_call, $provider);

            // Overwrite config file
            file_put_contents($provider_path, $provider);

            return $provider_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadMiddleware($template_path)
    {
        try {

            $data_map = $this->parseName();

            $middleware_path = app_path('Http/Middleware');


            $middlewares = array(
                [
                    'stub' => $template_path . '/Middleware/RedirectIfAuthenticated.stub',
                    'path' => $middleware_path . '/RedirectIf' . $data_map['{{singularClass}}'] . '.php',
                ],
                [

                    'stub' => $template_path . '/Middleware/RedirectIfNotAuthenticated.stub',
                    'path' => $middleware_path . '/RedirectIfNot' . $data_map['{{singularClass}}'] . '.php',
                ],
                [
                    'stub' => $template_path . '/Middleware/EnsureEmailIsVerified.stub',
                    'path' => $middleware_path . '/Ensure' . $data_map['{{singularClass}}'] . 'EmailIsVerified.php',
                ],
            );

            foreach ($middlewares as $middleware) {
                $stub = file_get_contents($middleware['stub']);
                file_put_contents($middleware['path'], strtr($stub, $data_map));
            }

            return $middleware_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function registerRouteMiddleware($stub_path)
    {
        try {

            $data_map = $this->parseName();

            $kernel_path = app_path('Http/Kernel.php');

            $kernel = file_get_contents($kernel_path);

            /********** Route Middleware **********/

            $route_mw = file_get_contents($stub_path . '/Middleware/Kernel.stub');

            $route_mw = strtr($route_mw, $data_map);

            $route_mw_bait = 'protected $routeMiddleware = [';

            $kernel = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $kernel);

            // Overwrite config file
            file_put_contents($kernel_path, $kernel);

            return $kernel_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }



}
