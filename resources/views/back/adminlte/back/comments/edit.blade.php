@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.edition'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>{{ Lang::get('administrable::messages.default.edition') }}</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . 'dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('comment.show', $comment) }}">{{ $comment->getCommenterName() }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.default.edition') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ Lang::get('administrable::messages.default.edition') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-12'>
                       @include(back_view_path('comments._form'), ['edit' => true])
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
