@extends('errors.layout')

@section('title', '422 — Données invalides')
@section('code', '422')
@section('icon-bg', 'bg-yellow-500/20')
@section('icon', 'fas fa-exclamation-circle')
@section('icon-color', 'text-yellow-400')
@section('heading', 'Données invalides')
@section('message')
    La requête contient des données incorrectes ou une règle métier a été violée.
    @if($exception->getMessage())
        <br><span class="font-medium text-white">{{ $exception->getMessage() }}</span>
    @endif
@endsection
