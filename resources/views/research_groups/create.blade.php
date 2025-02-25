@extends('dashboards.users.layouts.user-dash-layout')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card" style="padding: 16px;">
            <div class="card-body">
                <h4 class="card-title">สร้างกลุ่มวิจัย</h4>
                <p class="card-description">กรอกข้อมูลแก้ไขรายละเอียดกลุ่มวิจัย</p>
                <form action="{{ route('researchGroups.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <p class="col-sm-3 "><b>ชื่อกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_th" value="{{ old('group_name_th') }}" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (ภาษาไทย)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3 "><b>ชื่อกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_en" value="{{ old('group_name_en') }}" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (English)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_th" value="{{ old('group_desc_th') }}" class="form-control" style="height:90px"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_en" value="{{ old('group_desc_en') }}" class="form-control" style="height:90px"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_en" value="{{ old('group_detail_th') }}" class="form-control" style="height:90px"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_en" value="{{ old('group_detail_en') }}" class="form-control" style="height:90px"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>image</b></p>
                        <div class="col-sm-8">
                            <input type="file" name="group_image" class="form-control" value="{{ old('group_image') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>หัวหน้ากลุ่มวิจัย</b></p>
                        <div class="col-sm-8">
                            <select id='head0' name="head">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->fname_th }} {{ $user->lname_th }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3 pt-4"><b>สมาชิกกลุ่มวิจัย</b></p>
                        <div class="col-sm-8">
                            <table class="table" id="dynamicAddRemove">
                                <tr>
                                    <th>Member</th>
                                    <th>Role</th>
                                    <th>Permission</th>
                                    <th><button type="button" name="add" id="add-btn2"
                                            class="btn btn-success btn-sm add"><i class="mdi mdi-plus"></i></button></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary upload mt-5">Submit</button>
                    <a class="btn btn-light mt-5" href="{{ route('researchGroups.index') }}"> Back</a>
                </form>
            </div>
        </div>
    </div>

@stop
@section('javascript')
    <script>
        $(document).ready(function() {
            $("#selUser0").select2()
            $("#head0").select2()

            var i = 0;

            $("#add-btn2").click(function() {
                ++i;
                var userOptions =
                    '@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->fname_th }} {{ $user->lname_th }}</option>@endforeach';
                var roleOptions = '<option value="2">Member</option>' +
                    '<option value="3">Post-Doc</option>' +
                    '<option value="4">Visitor</option>';
                var permissionOptions = '<option value="2">View</option>' +
                    '<option value="1">Edit</option>';
                var newRow = '<tr>' +
                    '<td><select id="selUser' + i + '" name="moreFields[users][' + i +
                    '][userid]" style="width: 200px;">' +
                    '<option value="">Select User</option>' + userOptions + '</select></td>' +
                    '<td><select name="moreFields[users][' + i + '][role]" class="form-control">' +
                    roleOptions + '</select></td>' +
                    '<td><select name="moreFields[users][' + i + '][permission]" class="form-control">' +
                    permissionOptions + '</select></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>';
                $("#dynamicAddRemove").append(newRow);
                $("#selUser" + i).select2();
            });
            $(document).on('click', '.remove-tr', function() {
                $(this).parents('tr').remove();
            });

        });
    </script>
@stop
