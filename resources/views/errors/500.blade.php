@extends('errors.layout')

@section('title', '500 — Erreur serveur')
@section('code', '500')
@section('icon-bg', 'bg-red-600/20')
@section('icon', 'fas fa-server')
@section('icon-color', 'text-red-400')
@section('heading', 'Erreur interne du serveur')
@section('message')
    Une erreur inattendue s'est produite côté serveur.
    L'incident a été enregistré automatiquement.
    Veuillez réessayer dans quelques instants.
@endsection

@section('details')
    Si le problème persiste, contactez l'équipe technique en précisant
    l'heure de l'erreur : <strong class="text-indigo-200">{{ now()->format('d/m/Y à H:i:s') }}</strong>
@endsection
