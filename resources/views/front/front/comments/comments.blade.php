@php
    if ((isset($approved) && $approved) || $model->approved) {
        $comments = $model->approvedComments;
    }else {
        $comments = $model->comments;
    }
@endphp

@if($comments->count() < 1)
<div class="alert alert-warning">Pas encore de commentaires. Soyez le premier à commenter.</div>
@endif

<ul class="list-unstyled">
    @php
        $comments = $comments->sortBy('created_at');

        if (isset($perPage)) {
            $page = request()->query('page', 1) - 1;

            $parentComments = $comments->where('child_id', '');

            $slicedParentComments = $parentComments->slice($page * $perPage, $perPage);

            // $m = App\Mo; // This has to be done like this, otherwise it will complain.
            $modelKeyName = (new (AdminModule::model('comment')))->getKeyName(); // This defaults to 'id' if not changed.

            $slicedParentCommentsIds = $slicedParentComments->pluck($modelKeyName)->toArray();

            // Remove parent Comments from comments.
            $comments = $comments->where('child_id', '!=', '');

            $grouped_comments = new \Illuminate\Pagination\LengthAwarePaginator(
                $slicedParentComments->merge($comments)->groupBy('child_id'),
                $parentComments->count(),
                $perPage
            );

            $grouped_comments->withPath('/' . request()->path());
        } else {
            $grouped_comments = $comments->groupBy('child_id');
        }
    @endphp

    @foreach($grouped_comments as $comment_id => $comments)
        {{-- Process parent nodes --}}
        @if($comment_id == '')
            @foreach($comments as $comment)
                @include('administrable::front.comments._comment', [
                    'comment'          => $comment,
                    'grouped_comments' => $grouped_comments
                ])
            @endforeach
        @endif
    @endforeach
</ul>

@isset ($perPage)
<hr>
<div class="d-flex justify-content-end">
    {{ $grouped_comments->links() }}
</div>
@endisset

@auth('admin')
    @include('administrable::front.comments._form', [
        'admin' => get_admin()
    ])
@else
    @auth
        @include('administrable::front.comments._form', [
            'user' => Auth::user()
        ])
    @elseif(config('administrable.comments.guest_commenting') == true)
        @include('administrable::front.comments._form', [
            'guest_commenting' => true
        ])
    @else
        <div class="card border-danger">
            <div class="card-body text-danger text-center">
                <h5 class="card-title">Authentification requise</h5>
                <p class="card-text">Vous devez etre connecté pour commenter.</p>
                <a href="{{ route('login') }}" class="btn btn-primary"><i class="fa fa-sign-in"></i>&nbsp; Se connecter</a>
            </div>
        </div>
    @endauth
@endauth


