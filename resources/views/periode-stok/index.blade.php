@extends('layouts.app')

@section('title', 'Periode Stok Opname')
@section('page-title', 'Periode Stok Opname')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- HEADER --}}
<div class="content-card mb-4">

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h5 class="mb-1 fw-semibold">
                Periode Stok Opname
            </h5>

            <p class="text-muted mb-0">
                Kelola periode pelaksanaan stok opname.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalTambah">

            <i class="bi bi-plus-lg me-1"></i>
            Buat Periode

        </button>

    </div>

</div>


{{-- TABLE --}}
<div class="content-card">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead>

                <tr>

                    <th width="60">No</th>

                    <th>Periode</th>

                    <th>Tanggal Opname</th>

                    <th>Status</th>

                    <th>Data Opname</th>

                    <th>Dibuat Oleh</th>

                    <th>Keterangan</th>

                    <th width="150">Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse($periodes as $periode)

                    <tr>

                        <td>
                            {{ $periodes->firstItem() + $loop->index }}
                        </td>

                        <td>

                            <span class="fw-semibold">
                                {{ $periode->periode }}
                            </span>

                        </td>

                        <td>
                            {{ $periode->tanggal_opname->format('d/m/Y') }}
                        </td>

                        <td>

                            @if($periode->status === 'draft')

                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-pencil me-1"></i>
                                    Draft
                                </span>

                            @else

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Selesai
                                </span>

                            @endif

                        </td>

                        <td>

                            <span class="badge bg-light text-dark border">

                                {{ $periode->details_count }}

                                Barang

                            </span>

                        </td>

                        <td>
                            {{ $periode->user->name ?? '-' }}
                        </td>

                        <td>
                            {{ $periode->keterangan ?: '-' }}
                        </td>

                        <td>

                            <div class="d-flex gap-1">

                                @if($periode->status === 'draft')

                                    <button type="button"
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $periode->id }}"
                                            title="Edit">

                                        <i class="bi bi-pencil"></i>

                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapus{{ $periode->id }}"
                                            title="Hapus">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                @else

                                    <span class="text-muted small">
                                        Terkunci
                                    </span>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8"
                            class="text-center text-muted py-5">

                            <i class="bi bi-calendar3 fs-2 d-block mb-2"></i>

                            Belum ada periode stok opname.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($periodes->hasPages())

        <div class="mt-4">
            {{ $periodes->links('components.pagination.limited') }}
        </div>

    @endif

</div>


{{-- =====================================================
     MODAL TAMBAH
===================================================== --}}

<div class="modal fade"
     id="modalTambah"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('periode-stok-opname.store') }}"
                  method="POST">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-calendar-plus me-2"></i>

                        Buat Periode Stok Opname

                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">

                                Periode
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="periode"
                                   class="form-control"
                                   placeholder="Contoh: September 2026"
                                   required>

                            <small class="text-muted">
                                Gunakan nama periode yang mudah dikenali.
                            </small>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">

                                Tanggal Opname
                                <span class="text-danger">*</span>

                            </label>

                            <input type="date"
                                   name="tanggal_opname"
                                   class="form-control"
                                   value="{{ date('Y-m-d') }}"
                                   required>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Keterangan
                            </label>

                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Keterangan periode (opsional)"></textarea>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =====================================================
     MODAL EDIT
===================================================== --}}

@foreach($periodes as $periode)

    @if($periode->status === 'draft')

        <div class="modal fade"
             id="modalEdit{{ $periode->id }}"
             tabindex="-1">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form action="{{ route('periode-stok-opname.update', $periode->id) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">

                            <h5 class="modal-title">

                                <i class="bi bi-pencil-square me-2"></i>

                                Edit Periode Stok Opname

                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>

                        </div>


                        <div class="modal-body">

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">

                                        Periode
                                        <span class="text-danger">*</span>

                                    </label>

                                    <input type="text"
                                           name="periode"
                                           class="form-control"
                                           value="{{ $periode->periode }}"
                                           required>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">

                                        Tanggal Opname
                                        <span class="text-danger">*</span>

                                    </label>

                                    <input type="date"
                                           name="tanggal_opname"
                                           class="form-control"
                                           value="{{ $periode->tanggal_opname->format('Y-m-d') }}"
                                           required>

                                </div>


                                <div class="col-12">

                                    <label class="form-label">
                                        Keterangan
                                    </label>

                                    <textarea name="keterangan"
                                              class="form-control"
                                              rows="3">{{ $periode->keterangan }}</textarea>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                Batal

                            </button>

                            <button type="submit"
                                    class="btn btn-primary">

                                <i class="bi bi-save me-1"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

@endforeach


{{-- =====================================================
     MODAL HAPUS
===================================================== --}}

@foreach($periodes as $periode)

    @if($periode->status === 'draft')

        <div class="modal fade"
             id="modalHapus{{ $periode->id }}"
             tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <form action="{{ route('periode-stok-opname.destroy', $periode->id) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <div class="modal-header">

                            <h5 class="modal-title text-danger">

                                <i class="bi bi-exclamation-triangle me-2"></i>

                                Hapus Periode

                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>

                        </div>


                        <div class="modal-body text-center py-4">

                            <i class="bi bi-trash3 text-danger fs-1"></i>

                            <p class="mt-3 mb-1">

                                Yakin ingin menghapus periode?

                            </p>

                            <strong>
                                {{ $periode->periode }}
                            </strong>

                            <p class="text-muted small mt-2 mb-0">

                                Periode hanya dapat dihapus
                                selama masih berstatus Draft.

                            </p>

                        </div>


                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                Batal

                            </button>

                            <button type="submit"
                                    class="btn btn-danger">

                                <i class="bi bi-trash me-1"></i>

                                Hapus

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

@endforeach

@endsection