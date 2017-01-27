<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //打印数据库查询的语句,只有开启env文件中APP_DEBUG和DB_SHOW_SQL才现实执行的sql语句，用于调试开发的时候使用
        if(!empty(env('DB_SHOW_SQL')) && !empty(env('APP_DEBUG')))
        {
            DB::listen(function($sql) {
                foreach ($sql->bindings as $i => $binding)
                {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                $query = vsprintf($query, $sql->bindings);
                var_dump($query);
            });
        }


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
