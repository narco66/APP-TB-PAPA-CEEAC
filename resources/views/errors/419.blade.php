@extends('errors.layout')

@section('title', '419 — Session expirée')
@section('code', '419')
@section('icon-bg', 'bg-orange-500/20')
@section('icon', 'fas fa-clock')
@section('icon-color', 'text-orange-400')
@section('heading', 'Session expirée')
@section('message')
    Votre session a expiré ou le jeton de sécurité est invalide.
    Rechargez la page et réessayez.
@endsection

@section('details')
    Cela peut arriver si vous êtes resté inactif trop longtemps
    ou si plusieurs onglets sont ouverts simultanément.
@endsection
