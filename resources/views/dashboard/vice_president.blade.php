@extends('layouts.app')
@section('title', 'Dashboard VP')
@section('page-title', 'Arbitrage Stratégique — ' . $papa->code)
@section('content')
@include('dashboard.president')
@endsection
