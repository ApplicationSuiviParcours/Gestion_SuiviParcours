<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Note;
use App\Observers\NoteObserver;

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
   public function boot(): void
    {
        Note::observe(NoteObserver::class);
    }
}
