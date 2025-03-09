@extends('dashboards.users.layouts.user-dash-layout')
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.bootstrap4.min.css">

@section('content')

<div class="container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="card" style="padding: 16px;">
        <div class="card-body">
            <h4 class="card-title">Recruitment Announcement</h4>

            <!-- ปุ่มเพิ่มประกาศรับสมัคร -->
            
            <a class="btn btn-primary btn-menu btn-icon-text btn-sm mb-3" href="{{ route('recruitment.create') }}">
                <i class="mdi mdi-plus btn-icon-prepend"></i> ADD 
            </a>
            

            <!-- ตรวจสอบว่ามีประกาศหรือไม่ -->
            @if($recruitments->isEmpty())
                <p class="text-center text-muted">ยังไม่มีประกาศรับสมัคร</p>
            @else
            <table id="example1" class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Research Group</th>
                        <th>Title</th>
                        <th width="180px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recruitments as $i => $recruitment)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $recruitment->researchGroup->group_name_th ?? '-' }}</td>
                        <td>{{ $recruitment->title_th }}</td>
                        <td>
                            <form action="{{ route('recruitment.destroy', $recruitment->id) }}" method="POST">
                                <!-- ปุ่มดูรายละเอียด -->
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('recruitment.show', $recruitment->id) }}" 
                                    data-toggle="tooltip" title="View">
                                    <i class="mdi mdi-eye"></i>
                                </a>

                                <!-- ปุ่มแก้ไข -->
                                {{-- @if(Auth::user()->can('update', $recruitment)) --}}
                                <a class="btn btn-outline-success btn-sm" href="{{ route('recruitment.edit', $recruitment->id) }}" 
                                    data-toggle="tooltip" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                {{-- @endif --}}

                                <!-- ปุ่มลบ -->
                                {{-- @if(Auth::user()->can('delete', $recruitment)) --}}
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm show_confirm" type="submit" 
                                    data-toggle="tooltip" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                                {{-- @endif --}}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="http://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.datatables.net/1.12.0/js/dataTables.bootstrap4.min.js" defer></script>
<script src="https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js" defer></script>

<script>
    $(document).ready(function() {
        $('#example1').DataTable({
            responsive: true,
        });
    });

    $('.show_confirm').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
                title: `Are you sure?`,
                text: "If you delete this, it will be gone forever.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    swal("Delete Successfully", {
                        icon: "success",
                    }).then(function() {
                        location.reload();
                        form.submit();
                    });
                }
            });
    });
</script>

@stop
