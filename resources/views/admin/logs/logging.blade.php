@extends('template.admin')


@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Logs Aktivitas</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Logs</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($log->user_id)
                                        {{ $log->user->name ?? 'User Tidak Ditemukan' }}
                                    @elseif($log->member_id)
                                        {{ $log->member->nama ?? 'Member Tidak Ditemukan' }}
                                    @else
                                        Guest
                                    @endif
                                </td>

                                <td>{{ $log->aktivitas }}</td>
                                <td>{{ $log->deskripsi }}</td>
                                <td>{{ $log->tanggal_aktivitas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
