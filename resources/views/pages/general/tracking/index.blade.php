@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="card p-4 shadow rounded-4">
        <form method="GET" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-1">
                    <label class="form-label">Pencarian</label>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-1 text-center">
                    <label class="form-label">s/d</label>
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <select name="subject" class="form-select">
                        <option value="">Hal</option>
                        @foreach($allSubjects as $subject)
                            <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>
                                {{ $subject }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="keyword" class="form-control" placeholder="Kata Kunci" value="{{ request('keyword') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('tracking') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal dibuat</th>
                        <th>Dari</th>
                        <th>Kepada</th>
                        <th>Hal</th>
                        <th>Status</th>
                        <th>Pemeriksa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trackings as $i => $tracking)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($tracking->issued_date)->format('d F Y') }}</td>
                        <td>{{ $tracking->sender }}</td>
                        <td>{{ $tracking->recipient }}</td>
                        <td>{{ $tracking->subject }}</td>
                        <td>
                            <span class="badge 
                                @if($tracking->status === 'approved') bg-success
                                @elseif($tracking->status === 'rejected') bg-danger
                                @else bg-warning text-dark @endif"
                                title="Created: {{ $tracking->created_at->format('d F Y H:i') }} | Updated: {{ $tracking->updated_at->format('d F Y H:i') }}">
                                @if($tracking->status === 'approved')
                                    ✓ Approved
                                @elseif($tracking->status === 'rejected')
                                    ✗ Rejected
                                @else
                                    ⏳ Submission
                                @endif
                            </span>
                            @if($tracking->created_at != $tracking->updated_at)
                                <small class="text d-block" style="font-size: 0.7em;">
                                    (Updated: {{ $tracking->updated_at->format('d/m H:i') }})
                                </small>
                            @endif
                        </td>
                        <td>{{ $tracking->reviewer }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $trackings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection