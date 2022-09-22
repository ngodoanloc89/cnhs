@if (!empty($big))
    <div class="page-header mt-30 mb-75">
        <div class="container">
            <div class="archive-header" @if (!empty($background)) style="background-image: url({{ RvMedia::getImageUrl($background) }})" @endif>
                <div class="row align-items-center">
                    <div class="col-xl-3">
                        <h1 class="mb-15">{{ SeoHelper::getTitle() }}</h1>
                        <div class="breadcrumb">
                            @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
                                @if ($i != (count($crumbs) - 1))
                                    <a href="{{ $crumb['url'] }}" rel="nofollow" itemprop="item" title="{{ $crumb['label'] }}">
                                        @if ($i == 0)
                                            <i class="fi-rs-home mr-5"></i>
                                        @endif
                                        {{ $crumb['label'] }}
                                        <meta itemprop="name" content="{{ $crumb['label'] }}" />
                                        <meta itemprop="position" content="{{ $i + 1}}" />
                                    </a>
                                    <span></span>
                                @else
                                    {{ $crumb['label'] }}
                                    <meta itemprop="name" content="{{ $crumb['label'] }}" />
                                    <meta itemprop="position" content="{{ $i + 1}}" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
                    @if ($i != (count($crumbs) - 1))
                        <a href="{{ $crumb['url'] }}" itemprop="item" title="{{ $crumb['label'] }}">
                            {{ $crumb['label'] }}
                            <meta itemprop="name" content="{{ $crumb['label'] }}" />
                            <meta itemprop="position" content="{{ $i + 1}}" />
                        </a>
                        <span></span>
                    @else
                        {{ $crumb['label'] }}
                        <meta itemprop="name" content="{{ $crumb['label'] }}" />
                        <meta itemprop="position" content="{{ $i + 1}}" />
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
