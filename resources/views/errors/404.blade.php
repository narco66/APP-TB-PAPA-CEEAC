@extends('errors.layout')

@section('title', '404 — Page introuvable')
@section('code', '404')
@section('icon-bg', 'bg-amber-500/20')
@section('icon', 'fas fa-search')
@section('icon-color', 'text-amber-400')
@section('heading', 'Page introuvable')
@section('message')
    La ressource demandée n'existe pas ou a été supprimée.
    Vérifiez l'adresse ou revenez à la page précédente.
@endsection

@section('details')
    URL demandée : <code class="text-amber-200 font-mono text-xs">{{ request()->url() }}</code>
@endsection
