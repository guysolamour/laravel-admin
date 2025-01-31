@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.edition'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route(config('administrable.guard') . '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                           <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('comment.show', $comment) }}">{{ $comment->getCommenterName() }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.edition') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get('administrable::messages.default.edition') }}</h3>
                        <div class="btn-group">
                          @if(Route::has(back_view_path('extensions.blog.post.edit')))
                           <a href="{{ back_route('extensions.blog.post.edit', $comment->commentable) }}" class="btn btn-primary"><i class="fa fa-eye"></i> Voir l'article</a>
                           @endif
                            <a href="{{ back_route('comment.destroy', $comment) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}">
                                <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @include(back_view_path('comments._form'), ['edit' => true])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
