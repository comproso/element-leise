<?php

namespace Comproso\Elements\Leise;

use Illuminate\Support\ServiceProvider;

class ElementLeiseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // provide views
		$this->loadViewsFrom(base_path('resources/views/vendor/comproso/element-leise'), 'leise');

        // publish seeds
	    $this->publishes([
	        __DIR__.'/database/migrations' => base_path('database/migrations')
	    ], "migrations");

	    // publish views
	    $this->publishes([
	    	__DIR__.'/resources/views' => base_path('resources/views/vendor/comproso/element-leise')
	    ], 'views');

	    // publish assets
	    $this->publishes([
		    __DIR__.'/resources/assets' => public_path('vendor/comproso/element-leise')
	    ], "assets");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
