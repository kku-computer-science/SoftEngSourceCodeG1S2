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
                <h4 class="card-title">แก้ไขข้อมูลกลุ่มวิจัย</h4>
                <p class="card-description">กรอกข้อมูลแก้ไขรายละเอียดกลุ่มวิจัย</p>
                <form action="{{ route('researchGroups.update', $researchGroup->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <p class="col-sm-3"><b>ชื่อกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_th" value="{{ $researchGroup->group_name_th }}" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (ภาษาไทย)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>ชื่อกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_en" value="{{ $researchGroup->group_name_en }}" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (English)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_th" class="form-control" style="height:90px">{{ $researchGroup->group_desc_th }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_en" class="form-control" style="height:90px">{{ $researchGroup->group_desc_en }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_th" class="form-control" style="height:90px">{{ $researchGroup->group_detail_th }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_en" class="form-control"
                                style="height:90px">{{ $researchGroup->group_detail_en }}</textarea>
                        </div>
                    </div>
                    {{-- แก้ไข การอัพโหลดรูปภาพ --}}
                    <div class="form-group row">
                        <p class="col-sm-3"><b>image</b></p>
                        <div class="col-sm-8">
                            <input type="file" name="group_image" class="form-control" id="group_image"
                                accept="image/png, image/jpeg, image/jpg, image/gif">
                            <small class="text-muted">Allowed file types: .png, .jpeg, .jpg, .gif</small>
                            <p id="file-error" class="text-danger" style="display: none;">Invalid file type. Please upload
                                an image file.</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <p class="col-sm-3"><b>หัวหน้ากลุ่มวิจัย</b></p>
                        <div class="col-sm-8">
                            <select id='head0' name="head" class="form-control"
                                @if (!auth()->user()->hasRole('admin')) disabled @endif>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        @if ($researchGroup->user->contains('id', $user->id) && $researchGroup->user->where('id', $user->id)->first()->pivot->role == 1) selected @endif>
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
                                    <th>
                                        <button type="button" name="add" id="add-btn2"
                                            class="btn btn-success btn-sm add">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </table>
                        </div>
                        <p class="col-sm-3 pt-4"><b>Other Visitors</b></p>
                        <div class="col-sm-8">
                            <table class="table" id="AuthorsDynamicAddRemove">
                                <tr>
                                    <th>Member</th>
                                    <th>
                                        <button type="button" name="add" id="add-btn2-authors"
                                            class="btn btn-success btn-sm add">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-5">Submit</button>
                    <a class="btn btn-light mt-5" href="{{ route('researchGroups.index') }}"> Back</a>
                </form>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script>
        $(document).ready(function() {
            var isAdmin = @json(auth()->user()->hasRole('admin')); // ตรวจสอบว่าเป็น admin หรือไม่

            if (!isAdmin) {
                $("#head0").prop("disabled", true); // ถ้าไม่ใช่ admin ปิดการแก้ไข dropdown
            }
        });
        $(document).ready(function() {
            $("#head0").select2()
            $("#fund").select2()
            var j = 0;
            var researchGroup = <?php echo json_encode($researchGroup['user'] ?? []); ?>;
            var i = 0;
            for (i = 0; i < researchGroup.length; i++) {
                var obj = researchGroup[i];
                if (obj.pivot.role !== 1) {
                    var userOptions =
                        '@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->fname_th }} {{ $user->lname_th }}</option>@endforeach';
                    var roleOptions = '<option value="2" ' + (obj.pivot.role === 2 ? "selected" : "") +
                        '>Member</option>' +
                        '<option value="3" ' + (obj.pivot.role === 3 ? "selected" : "") + '>Post-Doc</option>' +
                        '<option value="4" ' + (obj.pivot.role === 4 ? "selected" : "") + '>Visitor</option>';

                        var isAdmin = @json(auth()->user()->hasRole('admin')); // ตรวจสอบว่าเป็น admin หรือไม่

                        var permissionOptions;
                        if (isAdmin) {
                            // Admin ใช้ dropdown แก้ไขได้
                            permissionOptions = '<select name="moreFields[users][' + i + '][permission]" class="form-control">' +
                                '<option value="2" ' + (obj.pivot.permissions === 2 ? "selected" : "") + '>View</option>' +
                                '<option value="1" ' + (obj.pivot.permissions === 1 ? "selected" : "") + '>Edit</option>' +
                            '</select>';
                        } else {
                            // สมาชิกทั่วไปแค่ดูได้ แต่แก้ไม่ได้
                            permissionOptions = '<input type="text" class="form-control" value="' + 
                                (obj.pivot.permissions === 1 ? "Edit" : "View") + '" readonly>' +
                                '<input type="hidden" name="moreFields[users][' + i + '][permission]" value="' + obj.pivot.permissions + '">';
                        }

                        $("#dynamicAddRemove").append(
                            '<tr>' +
                            '<td><select id="selUser' + i + '" name="moreFields[users][' + i +
                            '][userid]" style="width: 200px;">' + userOptions + '</select></td>' +
                            '<td><select name="moreFields[users][' + i + '][role]" class="form-control">' +
                            roleOptions + '</select></td>' +
                            '<td>' + permissionOptions + '</td>' + 
                            '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                            '</tr>'
                        );
                        document.getElementById("selUser" + i).value = obj.id;
                        $("#selUser" + i).select2();

                }
            }
            $("#add-btn2").click(function() {
                ++i;
                var userOptions =
                    '@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->fname_th }} {{ $user->lname_th }}</option>@endforeach';
                var roleOptions = '<option value="2">Member</option>' +
                    '<option value="3">Post-Doc</option>' +
                    '<option value="4">Visitor</option>';

                var isAdmin = @json(auth()->user()->hasRole('admin')); // เช็คว่าเป็น admin หรือไม่

                var permissionOptions;
                if (isAdmin) {
                    // ถ้าเป็น admin ให้ dropdown แก้ไข permission ได้
                    permissionOptions = '<select name="moreFields[users][' + i + '][permission]" class="form-control">' +
                        '<option value="2" selected>View</option>' + // ค่าเริ่มต้นเป็น View
                        '<option value="1">Edit</option>' + 
                    '</select>';
                } else {
                    // ถ้าไม่ใช่ admin ให้ค่า permission เป็น 2 (View) และปิดการแก้ไข
                    permissionOptions = '<input type="text" class="form-control" value="View" readonly>' +
                                        '<input type="hidden" name="moreFields[users][' + i + '][permission]" value="2">';
                }

                var newRow = '<tr>' +
                    '<td><select id="selUser' + i + '" name="moreFields[users][' + i +
                    '][userid]" style="width: 200px;">' +
                    '<option value="">Select User</option>' + userOptions + '</select></td>' +
                    '<td><select name="moreFields[users][' + i + '][role]" class="form-control">' +
                    roleOptions + '</select></td>' +
                    '<td>' + permissionOptions + '</td>' + 
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>';
                $("#dynamicAddRemove").append(newRow);
                $("#selUser" + i).select2();
            });

            var authors = <?php echo json_encode($researchGroup['author'] ?? []); ?>;
            for (var j = 0; j < authors.length; j++) {
                var author = authors[j];
                var authorOptions = '@foreach ($authors as $author)<option value="{{ $author->id }}">{{ $author->author_fname }} {{ $author->author_lname }}</option>@endforeach';
                $("#AuthorsDynamicAddRemove").append( '<tr>' +
                    '<td><select id="selAuthor' + j + '" name="authors[' + j +
                    '][userid]" style="width: 200px;">' + authorOptions + '</select></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>');
                document.getElementById("selAuthor" + j).value = author.id;
                $("#selAuthor" + j).select2();
            }
            $(document).on('click', '.remove-tr', function() {
                $(this).parents('tr').remove();
            });
            
            $("#add-btn2-authors").click(function() {
                ++j;
                var userOptions =
                    '@foreach ($authors as $author)<option value="{{ $author->id }}">{{ $author->author_fname }} {{ $author->author_lname }}</option>@endforeach';
                var newRow = '<tr>' +
                    '<td><select id="selAuthor' + j + '" name="authors[' + j +
                    '][userid]" style="width: 200px;">' +
                    '<option value="">Select User</option>' + userOptions + '</select></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>';
                $("#AuthorsDynamicAddRemove").append(newRow);
                $("#selAuthor" + j).select2();
            });
            $(document).on('click', '.remove-tr', function() {
                $(this).parents('tr').remove();
            });
        });
        $(document).ready(function() {
            $("#group_image").change(function() {
                var file = this.files[0];
                if (file) {
                    var fileType = file.type;
                    var validImageTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif"];
                    if (!validImageTypes.includes(fileType)) {
                        $("#file-error").show();
                        this.value = ""; // Reset file input
                    } else {
                        $("#file-error").hide();
                    }
                }
            });
        });
    </script>
@stop
