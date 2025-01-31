@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.comment.plural'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . 'dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
            </ol>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> {{ Lang::get('administrable::messages.view.comment.plural') }} </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ AdminModule::model('comment') }}"
                id="delete-all"><i class="fa fa-trash"></i> &nbsp;  {{ Lang::get('administrable::messages.default.deleteall') }}</a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>

               <th>#</th>
                <th>{{ Lang::get('administrable::messages.view.comment.name') }}</th>
                <th>{{ Lang::get('administrable::messages.view.comment.email') }}</th>
                <th>{{ Lang::get('administrable::messages.view.comment.content') }}</th>
                <th>{{ Lang::get('administrable::messages.view.comment.approved') }}</th>
                <th>{{ Lang::get('administrable::messages.view.comment.createdat') }}</th>
                {{-- add fields here --}}
                <th>{{ Lang::get('administrable::messages.view.comment.actions') }}</th>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $comment->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $comment->id }}"
                                id="check-{{ $comment->id }}" <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $comment->getCommenterName() }}</td>
                    <td>{{ $comment->getCommenterEmail() }}</td>
                    <td>{!! Str::limit(strip_tags($comment->comment),50) !!}</td>
                    <td>
                        @if ($comment->approved)
                            <a data-provide="tooltip" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-circle text-success"></i></a>
                        @else
                            <a data-provide="tooltip" title="{{ Lang::get('administrable::messages.view.comment.disapproved') }}"><i class="fas fa-circle text-secondary"></i></a>
                        @endif
                    </td>
                    <td>{{ $comment->created_at->format('d/m/Y h:i') }}</td>
                    {{-- add values here --}}
                    <td>

                        <div class="btn-group" role="group">
                            <a  href="{{ back_route('comment.show', $comment) }}" class="btn btn-primary"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                    class="fas fa-eye"></i></a>

                            @unless ($comment->approved)
                            <a href="{{ back_route('comment.approved', $comment) }}" class="btn btn-success"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i
                                    class="fas fa-check"></i></a>
                            @endunless

                            <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-info"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.edit') }}"><i
                                    class="fas fa-edit"></i></a>
                             <a href="#" class="btn btn-secondary" title="{{ Lang::get('administrable::messages.view.comment.reply') }}" data-toggle="modal"
                                    data-target="#answerModal{{ $comment->getKey() }}"><i class="fas fa-undo"></i></a>
                            <a href="{{ back_route('comment.destroy',$comment) }}" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}"
                                class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                title="{{ Lang::get('administrable::messages.default.default') }}"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <div class="modal fade" id="answerModal{{ $comment->getKey() }}" tabindex="-1"
                    aria-labelledby="answerModal{{ $comment->getKey() }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="answerModal{{ $comment->getKey() }}Label">Répondre au commentaire de
                                    :<i>`{{ $comment->getCommenterName() }}`</i></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-secondary">
                                    {{ strip_tags($comment->comment) }}
                                </div>
                                <form action="{{ back_route('comment.reply', $comment) }}" method="post"
                                    id="answerComment{{ $comment->getKey() }}">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $comment->getKey() }}">

                                    <div class="form-group">
                                        <input type="text" name="guest_name" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.name') }}"
                                            value="{{ get_guard('full_name') }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="guest_email" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.email') }}"
                                            value="{{ get_guard('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="comment" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.answer') }}"
                                            rows="10" required></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ Lang::get('administrable::messages.default.cancel') }}</button>
                                <button type="submit" form="answerComment{{ $comment->getKey() }}" class="btn btn-primary"><i
                                        class="fa fa-plus"></i>  {{ Lang::get('administrable::messages.view.comment.reply') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))

@endsection
