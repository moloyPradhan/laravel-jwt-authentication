@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@section('content')

    <div>
        <h1>Dashboard</h1>
    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';
    </script>

@endsection
