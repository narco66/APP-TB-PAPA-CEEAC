@extends('errors.layout')

@section('title', '403 — Accès refusé')
@section('code', '403')
@section('icon-bg', 'bg-red-500/20')
@section('icon', 'fas fa-ban')
@section('icon-color', 'text-red-400')
@section('heading', 'Accès non autorisé')
@section('message')
    Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.
    @if($exception->getMessage() && $exception->getMessage() !== 'This action is unauthorized.')
        <br><span class="font-medium text-white">{{ $exception->getMessage() }}</span>
    @endif
@endsection

@section('details')
    @if(auth()->check())
        Votre rôle actuel (<strong>{{ auth()->user()->getRoleNames()->first() ?? 'inconnu' }}</strong>)
        ne dispose pas du droit requis pour cette action.
        Contactez un administrateur si vous pensez qu'il s'agit d'une erreur.
    @else
        Vous devez être connecté avec les droits appropriés pour accéder à cette page.
    @endif
@endsection
