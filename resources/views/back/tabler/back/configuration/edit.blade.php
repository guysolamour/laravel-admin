@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.configuration.plural'))

@section('content')
<div class="row mb-5">
  <div class="col-12">
    <div class="d-flex justify-content-between">
        <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
            <li class="breadcrumb-item"><a href="{{ route( config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.configuration.plural') }}</a></li>
        </ol>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card-body p-0">
      {!! form_start($form) !!}
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->email) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->postal) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->area) !!}
            </div>
            <div class="col-md-6">
                <div class="form-group row pt-3">
                  <div class="col-md-3">
                      <img class="img-fluid" src="{{ $configuration->logo }}" alt="{{ config('app.name') }}">
                  </div>
                  <div class="col-md-9">
                      {!! form_widget($form->logo) !!}
                  </div>

              </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                {!! form_row($form->cell) !!}
            </div>
            <div class="col-md-4">
                {!! form_row($form->phone) !!}
            </div>
            <div class="col-md-4">
                {!! form_row($form->whatsapp) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->facebook) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->twitter) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->linkedin) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->youtube) !!}
            </div>
        </div>
        {!! form_rest($form) !!}
        {{-- add fields here --}}
          <div class="col-md-12">
          <div class="form-group">
            <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-edit"></i>
               {{ Lang::get('administrable::messages.default.save') }}</button>
          </div>
        </div>
        {!! form_end($form) !!}
    </div>
  </div>
</div>
@endsection

<x-administrable::custominputfile selector="input[name=logo]" />
