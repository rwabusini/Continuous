@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-qr-panel title="On-site QR" :token="$payload['onsite']"/>

    @if($payload['remote'])
        <x-qr-panel title="Remote QR" :token="$payload['remote']"/>
    @endif

    <section class="col-span-1 md:col-span-2 space-y-4">
        <button id="generate-keyword" class="btn btn-primary">Generate Remote Keyword</button>
        <div id="keyword-display" class="text-3xl font-mono"></div>
    </section>

    <section class="col-span-1 md:col-span-2">
        <livewire:attendance-risk-table :session="$session->id" />
    </section>
</div>
@endsection
