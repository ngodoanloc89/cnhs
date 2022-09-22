<?php

namespace Botble\Blog\Services;

use Botble\ACL\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Blog\Models\Post;
use Botble\Blog\Services\Abstracts\StoreTypeServiceAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreTypeService extends StoreTypeServiceAbstract
{
    /**
     * @param Request $request
     * @param Post $post
     * @return mixed|void
     */
    public function execute(Request $request, Post $post)
    {
        $types = $post->types->pluck('name')->all();

        $typesInput = collect(json_decode($request->input('type'), true))->pluck('value')->all();

        if (count($types) != count($typesInput) || count(array_diff($types, $typesInput)) > 0) {
            $post->types()->detach();
            foreach ($typesInput as $typeName) {
                if (!trim($typeName)) {
                    continue;
                }

                $type = $this->typeRepository->getFirstBy(['name' => $typeName]);

                if ($type === null && !empty($typeName)) {
                    $type = $this->typeRepository->createOrUpdate([
                        'name'        => $typeName,
                        'author_id'   => Auth::check() ? Auth::id() : 0,
                        'author_type' => User::class,
                    ]);

                    $request->merge(['slug' => $typeName]);

                    event(new CreatedContentEvent(type_MODULE_SCREEN_NAME, $request, $type));
                }

                if (!empty($type)) {
                    $post->types()->attach($type->id);
                }
            }
        }
    }
}
