<?php

namespace RuLong\UserAccount;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use RuLong\UserAccount\Events\UserCreated;

class ServiceProvider extends LaravelServiceProvider
{

    /**
     * 部署时加载
     * @Author:<C.Jason>
     * @Date:2018-06-22T16:01:20+0800
     * @return [type] [description]
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/user_account.php' => config_path('user_account.php')]);
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

            $this->commands([
                Commands\InitAccounts::class,
            ]);
        }
    }

    /**
     * 注册服务提供者
     * @Author:<C.Jason>
     * @Date:2018-06-22T16:01:12+0800
     * @return [type] [description]
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/user_account.php', 'user_account');

        $this->registerEvents();
    }

    public function registerEvents()
    {
        // 直接监听基础用户模型的创建事件
        Event::listen("eloquent.created: " . config('user_account.user_model'), function ($user) {
            event(new UserCreated($user));
        });
    }
}
