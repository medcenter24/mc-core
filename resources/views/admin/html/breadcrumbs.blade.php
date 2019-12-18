@if ( isset ($menuService) && count($menuService->asArray()) )
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @include('admin.html.breadcrumbs.items', ['menu' => [[
                'active' => true,
                'slug' => 'admin',
                'name' => trans('content.main'),
            ]]])
            @include('admin.html.breadcrumbs.items', ['menu' => $menuService->asArray()])
        </ol>
    </nav>
@endif
