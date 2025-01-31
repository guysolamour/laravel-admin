@component('mail::message')
Bonjour {{ $admin->full_name }}.
Un commentaire  vient d'être déposé sur le site,
par <b>{{ $comment->getCommenterName() }}</b> joignable à l'adresse <b>{{ $comment->getCommenterEmail() }}</b>

@slot('title')
Nouveau commentaire de :   {{ $comment->getCommenterName() }}
@endslot

@component('mail::panel')
{{ Str::limit($comment->comment, 500) }}
@endcomponent

@component('mail::button', ['url' => back_route('comment.show',$comment)])
Voir le commentaire
@endcomponent



Merci, de bien vouloir valider ce commentaire <br>
{{ config('app.name') }}
@endcomponent

