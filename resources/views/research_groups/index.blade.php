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
            <h4 class="card-title">กลุ่มวิจัย</h4>
            @can('create', App\Models\ResearchGroup::class)
            <a class="btn btn-primary btn-menu btn-icon-text btn-sm mb-3" href="{{ route('researchGroups.create') }}"><i
                class="mdi mdi-plus btn-icon-prepend"></i> ADD</a>
            @endcan
            <!-- <div class="table-responsive"> -->
                <table id ="example1" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group name (ไทย)</th>
                            <th>Head</th>
                            <th>Member</th>
                            <th>Post-Doctoral</th>
                            <th>Visiting</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($researchGroups as $i=>$researchGroup)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 17vw; max-width: 17vw; line-height: 1.6; padding: 0.5em 0.75em;">
                                {{ $researchGroup->group_name_th }}
                            </td>                            
                            <td>
                                @foreach($researchGroup->user as $user)
                                @if ( $user->pivot->role == 1)

                                {{ $user->fname_th}}

                                @endif

                                @endforeach
                            </td>
                            <td style="white-space: normal; overflow: hidden; min-width: 8vw; line-height: 1.6; padding: 0.5em 0.75em;">
                                @php
                                    $members = collect($researchGroup->user)->filter(function($user) {
                                        return $user->pivot->role == 2;
                                    });
                                @endphp
                            
                                {{ $members->pluck('fname_th')->implode(', ') }}
                            </td>                            
                            <td style="white-space: normal; overflow: hidden; min-width: 8vw; line-height: 1.6; padding: 0.5em 0.75em;">
                                @php
                                    $postDocs = collect($researchGroup->user)->filter(function($user) {
                                        return $user->pivot->role == 3;
                                    });
                                @endphp
                            
                                {{ $postDocs->pluck('fname_th')->implode(', ') }}
                            </td>
                            <td style="white-space: normal; overflow: hidden; min-width: 8vw; line-height: 1.6; padding: 0.5em 0.75em;">
                                @php
                                    $visitingAuthors = $researchGroup->author->pluck('author_fname')->implode(', ');
                                @endphp
                            
                                {{ $visitingAuthors }}
                            </td>                                                
                            <td>
                                <form action="{{ route('researchGroups.destroy',$researchGroup->id) }}" method="POST">

                                    <a class="btn btn-outline-primary btn-sm" type="button" data-toggle="tooltip"
                                        data-placement="top" title="view"
                                        href="{{ route('researchGroups.show',$researchGroup->id) }}"><i
                                            class="mdi mdi-eye"></i></a>

                                    @if(Auth::user()->can('update',$researchGroup))
                                    <a class="btn btn-outline-success btn-sm" type="button" data-toggle="tooltip"
                                        data-placement="top" title="Edit"
                                        href="{{ route('researchGroups.edit',$researchGroup->id) }}"><i
                                            class="mdi mdi-pencil"></i></a>
                                    @endif

                                    @if(Auth::user()->can('delete',$researchGroup))
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm show_confirm" type="submit" data-toggle="tooltip"
                                        data-placement="top" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            <!-- </div> -->
        </div>
    </div>
    

</div>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src = "http://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer ></script>
<script src = "https://cdn.datatables.net/1.12.0/js/dataTables.bootstrap4.min.js" defer ></script>
<script src = "https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js" defer ></script>
<script>
    $(document).ready(function() {
        var table1 = $('#example1').DataTable({
            responsive: true,
        });
    });
</script>
<script type="text/javascript">
    $('.show_confirm').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
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