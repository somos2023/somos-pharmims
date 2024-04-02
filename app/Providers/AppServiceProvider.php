<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('date_range', function ($attribute, $value, $parameters, $validator) {
            $start_date = strtotime($validator->getData()[$parameters[0]]);
            $end_date = strtotime($value);

            return $start_date < $end_date;
        });

        Validator::replacer('date_range', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':start_date', $parameters[0], $message);
        });
    }
}
