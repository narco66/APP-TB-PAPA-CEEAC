@extends('layouts.app')
@section('title', 'Mon Tableau de bord')
@section('page-title', 'Mon suivi — ' . ($direction?->libelleAffichage() ?? ''))
@section('content')
@include('dashboard.direction')
@endsection
