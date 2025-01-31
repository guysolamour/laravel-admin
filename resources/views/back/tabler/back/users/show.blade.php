@extends(back_view_path('layouts.base'))

@section('title', $user->name)


@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('user.index') }}">{{ Lang::get('administrable::messages.view.user.plural') }}</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('user.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>&nbsp; {{ Lang::get('administrable::messages.default.edit') }}</a>
                <a href="{{ back_route('user.destroy', $user) }}" class="btn btn-danger"
                    data-method="delete" data-confirm="{{ Lang::get('administrable::messages.view.user.destroy') }}">
                    <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Lang::get('administrable::messages.view.user.plural') }}
        </h3>
       <div class="row">
           <div class='col-md-8'>
                {{-- add fields here --}}
                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.name') }}:</span></p>
                    <p>
                        {{ $user->name }}
                    </p>
                </div>

                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.pseudo') }}:</span></p>
                    <p>
                        {{ $user->pseudo }}
                    </p>
                </div>

                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.email') }}:</span></p>
                    <p>
                        {{ $user->email }}
                    </p>
                </div>

                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.createdat') }}:</span></p>
                    <p>
                        {{ $user->created_at->format('d/m/Y h:i') }}
                    </p>
                </div>


            </div>
            <div class='col-md-4'>
                @filemanagerShow([
                    'model'      =>  $user,
                    'collection' =>  'front-image',
                ])
            </div>
       </div>
    </div>
</div>
@endsection
