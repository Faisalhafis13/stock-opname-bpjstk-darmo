@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')


{{-- =========================================================
    SELAMAT DATANG
========================================================= --}}

<div class="content-card mb-4">

    <div class="welcome-section">

        <h4 class="fw-bold mb-1">
            Selamat Datang 👋
        </h4>

        <p class="text-muted mb-0">
            Sistem Informasi Stok Opname BPJS Ketenagakerjaan
        </p>

    </div>

</div>


{{-- =========================================================
    INFORMASI PENGGUNA
========================================================= --}}

<div class="content-card">

    <div class="card-body p-4">

        <h5 class="fw-semibold mb-4">
            Informasi Pengguna
        </h5>


        <div class="d-flex align-items-center gap-3">

            <div class="user-avatar">

                {{ strtoupper(
                    substr(
                        auth()->user()->name ?? 'A',
                        0,
                        1
                    )
                ) }}

            </div>


            <div>

                <div class="fw-semibold">

                    {{ auth()->user()->name ?? 'Administrator' }}

                </div>

                <small class="text-muted">

                    {{ auth()->user()->role->name ?? 'User' }}

                </small>

            </div>

        </div>

    </div>

</div>


@endsection


{{-- =========================================================
    STYLE
========================================================= --}}

@push('styles')

<style>

    .welcome-section {

        padding: 4px 0;

    }


    .user-avatar {

        width: 48px;

        height: 48px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background-color: #e9ecef;

        color: #495057;

        font-weight: 600;

        font-size: 18px;

    }

</style>

@endpush