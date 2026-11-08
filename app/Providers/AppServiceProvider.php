<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Gallery;
use App\Models\News;
use App\Models\TeacherType;
use App\Policies\MasterDataPolicy;
use App\Policies\SiteContentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Shared hosting (cPanel): folder public/ diletakkan langsung di document root
        // (mis. public_html) sedangkan aplikasi ada di subfolder. Aktifkan lewat
        // PUBLIC_PATH_PARENT=true di .env agar public_path() menunjuk ke document root.
        if (filter_var(env('PUBLIC_PATH_PARENT', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->app->usePublicPath(dirname($this->app->basePath()));

            // Upload gambar langsung disimpan ke <document root>/storage supaya
            // tidak perlu symlink storage.
            config(['filesystems.disks.public.root' => public_path('storage')]);
        }
    }

    public function boot(): void
    {
        Gate::policy(AcademicYear::class, MasterDataPolicy::class);
        Gate::policy(TeacherType::class, MasterDataPolicy::class);
        Gate::policy(News::class, SiteContentPolicy::class);
        Gate::policy(Gallery::class, SiteContentPolicy::class);

        Paginator::useTailwind();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
