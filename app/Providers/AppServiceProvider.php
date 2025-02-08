<?php

namespace App\Providers;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('Vendor')) {
                $projectss = Project::whereHas('vendor',function($query){
$query->with('user_id',auth()->user()->id);
                })->get();
            } else {
                $projectss = Project::whereHas('ProjectReview', function($query) {
                    $query->where('status_review', 'approved');
                })
                ->get();
            }
            // dd($projectss);

            // Pass the projects data to all views
            $view->with('projectss', $projectss);
        });
    }
}
