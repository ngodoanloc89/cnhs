<?php

namespace Botble\Blog\Providers;

use ApiHelper;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Shortcode\View\View;
use Illuminate\Routing\Events\RouteMatched;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Blog\Models\Post;
use Botble\Blog\Repositories\Caches\PostCacheDecorator;
use Botble\Blog\Repositories\Eloquent\PostRepository;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Botble\Blog\Models\Category;
use Botble\Blog\Repositories\Caches\CategoryCacheDecorator;
use Botble\Blog\Repositories\Eloquent\CategoryRepository;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;

use Botble\Blog\Models\Type;
use Botble\Blog\Repositories\Caches\TypeCacheDecorator;
use Botble\Blog\Repositories\Eloquent\TypeRepository;
use Botble\Blog\Repositories\Interfaces\TypeInterface;

use Botble\Blog\Models\Tag;
use Botble\Blog\Repositories\Caches\TagCacheDecorator;
use Botble\Blog\Repositories\Eloquent\TagRepository;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Language;
use Note;
use SeoHelper;
use SlugHelper;

/**
 * @since 02/07/2016 09:50 AM
 */
class BlogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(PostInterface::class, function () {
            return new PostCacheDecorator(new PostRepository(new Post()));
        });

        $this->app->bind(CategoryInterface::class, function () {
            return new CategoryCacheDecorator(new CategoryRepository(new Category()));
        });

        $this->app->bind(TypeInterface::class, function () {
            return new TypeCacheDecorator(new TypeRepository(new Type()));
        });

        $this->app->bind(TagInterface::class, function () {
            return new TagCacheDecorator(new TagRepository(new Tag()));
        });
    }

    public function boot()
    {
        SlugHelper::registerModule(Post::class, 'Văn bản');
        SlugHelper::registerModule(Category::class, 'Chuyên mục');
        SlugHelper::registerModule(Type::class, 'Loại văn bản');
        SlugHelper::registerModule(Tag::class, 'Blog Types');

        SlugHelper::setPrefix(Type::class, 'type', true);
        SlugHelper::setPrefix(Tag::class, 'tag', true);
        SlugHelper::setPrefix(Post::class, null, true);
        SlugHelper::setPrefix(Category::class, null, true);

        $this->setNamespace('plugins/blog')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web'])
            ->loadMigrations()
            ->publishAssets();

        if (ApiHelper::enabled()) {
            $this->loadRoutes(['api']);
        }

        $this->app->register(EventServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-blog',
                    'priority'    => 4,
                    'parent_id'   => null,
                    'name'        => 'Văn bản',
                    'icon'        => 'fa fa-edit',
                    'url'         => route('posts.index'),
                    'permissions' => ['posts.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-blog-post',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-blog',
                    'name'        => 'Văn bản',
                    'icon'        => null,
                    'url'         => route('posts.index'),
                    'permissions' => ['posts.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-blog-categories',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-blog',
                    'name'        => 'Danh mục',
                    'icon'        => null,
                    'url'         => route('categories.index'),
                    'permissions' => ['categories.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-blog-types',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-blog',
                    'name'        => 'Loại văn bản',
                    'icon'        => null,
                    'url'         => route('types.index'),
                    'permissions' => ['types.index'],
                ]);
        });

        $useLanguageV2 = $this->app['config']->get('plugins.blog.general.use_language_v2', false) &&
            defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME');

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && $useLanguageV2) {
            LanguageAdvancedManager::registerModule(Post::class, [
                'name',
                'description',
                'content',
            ]);

            LanguageAdvancedManager::registerModule(Category::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Type::class, [
                'name',
                'description',
            ]);
        }

        $this->app->booted(function () use ($useLanguageV2) {
            $models = [Post::class, Category::class, Type::class, Tag::class];

            if (defined('LANGUAGE_MODULE_SCREEN_NAME') && !$useLanguageV2) {
                Language::registerModule($models);
            }

            SeoHelper::registerModule($models);

            $configKey = 'packages.revision.general.supported';
            config()->set($configKey, array_merge(config($configKey, []), [Post::class]));

            if (defined('NOTE_FILTER_MODEL_USING_NOTE')) {
                Note::registerModule(Post::class);
            }

            $this->app->register(HookServiceProvider::class);
        });

        if (function_exists('shortcode')) {
            view()->composer([
                'plugins/blog::themes.post',
                'plugins/blog::themes.category',
                'plugins/blog::themes.type',
            ], function (View $view) {
                $view->withShortcodes();
            });
        }
    }
}
