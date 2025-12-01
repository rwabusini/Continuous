@extends('layouts.guest')

@section('content')
<div class="space-y-6">
    <section>
        <h1 class="text-2xl font-bold">Join Session</h1>
        <button id="scan-qr" class="btn btn-primary w-full mt-4">Scan QR</button>
        <button id="join-remote" class="btn btn-secondary w-full mt-2">Join remotely</button>
    </section>

    <section id="challenge-section" class="hidden">
        <p class="text-sm text-gray-600">Enter the live keyword shared by your trainer.</p>
        <input type="text" id="keyword" class="input w-full uppercase tracking-widest" maxlength="6">
        <button id="submit-keyword" class="btn btn-primary mt-2 w-full">Submit keyword</button>
    </section>

    <section>
        <p class="text-gray-500 text-sm">
            Keep this tab open; we will occasionally ask if you are still present.
        </p>
    </section>
</div>
@endsection
