<?php

namespace Botble\Blog\Repositories\Caches;

use Botble\Blog\Repositories\Interfaces\TypeInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class TypeCacheDecorator extends CacheAbstractDecorator implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDataSiteMap()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getPopularTypes($limit, array $with = ['slugable'], array $withCount = ['posts'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getAllTypes($active = true)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
