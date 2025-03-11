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
                        {{-- <p class="col-sm-3 pt-4"><b>Other Visitors</b></p>
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
                        </div> --}}
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>นักวิจัยภายนอก</b></p>
                        <div class="col-sm-8">
                            <div id="authors-container">
                                <!-- กล่อง Authors จะถูกเพิ่มที่นี่ -->
                            </div>
                            <button type="button" id="add-author-btn" class="btn btn-success btn-sm mt-2">
                                <i class="mdi mdi-plus"></i>
                            </button>
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
            $("#head0").select2();
            
            var isAdmin = @json(auth()->user()->hasRole('admin'));
            if (!isAdmin) {
                $("#head0").prop("disabled", true);
            }
            
            var i = 0;
            var researchGroup = @json($researchGroup['user'] ?? []);
            researchGroup.forEach(function(obj) {
                if (obj.pivot.role !== 1) {
                    appendMemberRow(i, obj.id, obj.pivot.role, obj.pivot.permissions);
                    i++;
                }
            });
            
            $("#add-btn2").click(function() {
                appendMemberRow(i, "", "2", "2");
                i++;
            });
            
            function appendMemberRow(index, userId, roleVal, permissionVal) {
                var userOptions = '@foreach ($users as $user)<option value="{{ $user->id }}" ' +
                                (userId == "{{ $user->id }}" ? "selected" : "") +
                                ' data-degree="{{ $user->doctoral_degree }}">{{ $user->fname_th }} {{ $user->lname_th }}</option>@endforeach';

                var postDocDisabled = "";
                @foreach ($users as $user)
                    if (userId == "{{ $user->id }}" && "{{ $user->doctoral_degree }}" !== "Ph.D.") {
                        postDocDisabled = "disabled";
                    }
                @endforeach

                var roleOptions = '<option value="2" ' + (roleVal == "2" ? "selected" : "") + '>Member</option>' +
                                '<option value="3" ' + (roleVal == "3" ? "selected" : "") + ' ' + postDocDisabled + '>Post-Doc</option>';

                var permissionOptions = isAdmin ? 
                    '<select name="moreFields[users][' + index + '][permission]" class="form-control" style="color: black;">' +
                        '<option value="2" ' + (permissionVal == "2" ? "selected" : "") + '>View</option>' +
                        '<option value="1" ' + (permissionVal == "1" ? "selected" : "") + '>Edit</option>' +
                    '</select>' :
                    '<input type="text" class="form-control" value="' + (permissionVal == "1" ? "Edit" : "View") + '" readonly>' +
                    '<input type="hidden" name="moreFields[users][' + index + '][permission]" value="' + permissionVal + '">';

                var newRow = '<tr>' +
                    '<td><select id="selUser' + index + '" name="moreFields[users][' + index + '][userid]" class="member-select form-control user-select" style="width: 200px; color: black;">' +
                    '<option value="">Select User</option>' + userOptions + '</select></td>' +
                    '<td><select name="moreFields[users][' + index + '][role]" class="form-control role-select" style="color: black;">' + roleOptions + '</select></td>' +
                    '<td>' + permissionOptions + '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>';

                $("#dynamicAddRemove").append(newRow);
                $("#selUser" + index).select2();

                if (userId) {
                    $("#selUser" + index).val(userId).trigger("change");
                }

                updateMemberOptions();
            }
            
            $(document).on("change", ".user-select", function() {
                var selectedUser = $(this).val();
                var $roleSelect = $(this).closest("tr").find(".role-select");

                // ตรวจสอบวุฒิการศึกษา
                var doctoralDegree = $(this).find(":selected").data("degree");
                var isPhD = (doctoralDegree === "Ph.D.");

                if (!isPhD) {
                    // ปิดการใช้งาน Post-Doc ถ้าผู้ใช้ไม่มี Ph.D.
                    $roleSelect.find("option[value='3']").prop("disabled", true);
                    if ($roleSelect.val() == "3") {
                        $roleSelect.val("2"); // ถ้าเลือกผิด ให้เปลี่ยนเป็น Member
                    }
                } else {
                    $roleSelect.find("option[value='3']").prop("disabled", false);
                }
            });

            $(document).on("click", ".remove-tr", function() {
                $(this).closest("tr").remove();
                updateMemberOptions();
            });
            
            $(document).on("change", ".member-select, #head0", function() {
                updateMemberOptions();
            });
            
            function updateMemberOptions() {
                var selectedValues = new Set();
                var headValue = $("#head0").val();
                if (headValue) {
                    selectedValues.add(headValue);
                }

                $(".member-select").each(function() {
                    var value = $(this).val();
                    if (value) {
                        selectedValues.add(value);
                    }
                });
                
                $(".member-select, #head0").each(function() {
                    var $this = $(this);
                    var currentValue = $this.val();
                    $this.find("option").each(function() {
                        var optionValue = $(this).val();
                        if (selectedValues.has(optionValue) && optionValue !== "" && optionValue !== currentValue) {
                            $(this).prop("disabled", true);
                        } else {
                            $(this).prop("disabled", false);
                        }
                    });
                });

                //ปิดใช้งาน Post-Doc สำหรับผู้ที่ไม่มี Ph.D.
                $(".user-select").each(function() {
                    var selectedUser = $(this).val();
                    var $roleSelect = $(this).closest("tr").find(".role-select");
                    var doctoralDegree = $(this).find(":selected").data("degree");

                    if (doctoralDegree !== "Ph.D.") {
                        $roleSelect.find("option[value='3']").prop("disabled", true);
                        if ($roleSelect.val() == "3") {
                            $roleSelect.val("2"); // เปลี่ยนเป็น Member ถ้าผิด
                        }
                    } else {
                        $roleSelect.find("option[value='3']").prop("disabled", false);
                    }
                });

                $("#head0").select2();
                $(".member-select").select2();
            }
            
            $("#head0").on("change", function(){
                updateMemberOptions();
            });

            $("form").submit(function(event) {
                $(".member-select").each(function() {
                    if ($(this).val() === "") {
                        $(this).closest("tr").remove();
                    }
                });
            });
            
            // Add authors functionality
            var j = 0;
            var authors = @json($researchGroup['author'] ?? []);
            authors.forEach(function(author) {
                appendAuthorRow(j, author.id);
                j++;
            });

            $("#add-btn2-authors").click(function() {
                appendAuthorRow(j, "");
                j++;
            });

            function appendAuthorRow(index, authorId) {
                var authorOptions = '@foreach ($authors as $author)<option value="{{ $author->id }}">{{ $author->author_fname }} {{ $author->author_lname }}</option>@endforeach';
                var newRow = '<tr>' +
                    '<td><select id="selAuthor' + index + '" name="authors[' + index + '][userid]" class="author-select form-control" style="width: 200px;">' +
                    '<option value="">Select Author</option>' + authorOptions + '</select></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="mdi mdi-minus"></i></button></td>' +
                    '</tr>';
                
                $("#AuthorsDynamicAddRemove").append(newRow);
                $("#selAuthor" + index).select2();
                if (authorId) {
                    $("#selAuthor" + index).val(authorId).trigger("change");
                }
                updateAuthorOptions();
            }
            
            $(document).on("click", ".remove-tr", function() {
                $(this).closest("tr").remove();
                updateAuthorOptions();
            });
            
            function updateAuthorOptions() {
                var selectedValues = new Set();
                $(".author-select").each(function() {
                    var value = $(this).val();
                    if (value) {
                        selectedValues.add(value);
                    }
                });
                
                $(".author-select").each(function() {
                    var $this = $(this);
                    var currentValue = $this.val();
                    $this.find("option").each(function() {
                        var optionValue = $(this).val();
                        if (selectedValues.has(optionValue) && optionValue !== "" && optionValue !== currentValue) {
                            $(this).prop("disabled", true);
                        } else {
                            $(this).prop("disabled", false);
                        }
                    });
                });
                
                $(".author-select").select2();
            }
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
        $(document).ready(function() {
            var authorIndex = 0;
            var authorsData = @json($authors);
            var existingAuthors = new Set();

            function appendAuthorRow(index, authorId = "", fname = "", lname = "", isNew = false) {
                var authorOptions = '<option value="">เลือก Visiting จากรายชื่อ</option>';
                authorsData.forEach(function(author) {
                    authorOptions += `<option value="${author.id}" ${author.id == authorId ? "selected" : ""}>
                                        ${author.author_fname} ${author.author_lname}
                                    </option>`;
                });

                var disabled = isNew ? "" : "disabled";

                var newRow = `
                    <div class="author-box p-3 mb-3" style="border: 1px solid #ddd; border-radius: 0.5em; box-shadow: none; background: transparent;">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>เลือกจากรายชื่อ</label>
                                <select class="form-control author-select" name="authors[${index}][userid]" id="author-select-${index}" data-index="${index}">
                                    ${authorOptions}
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>ชื่อ</label>
                                <input type="text" name="authors[${index}][author_fname]" class="form-control author-fname" id="author-fname-${index}" value="${fname}" ${disabled} placeholder="กรอกชื่อหากไม่มีในรายชื่อ">
                            </div>
                            <div class="col-sm-4">
                                <label>นามสกุล</label>
                                <input type="text" name="authors[${index}][author_lname]" class="form-control author-lname" id="author-lname-${index}" value="${lname}" ${disabled} placeholder="กรอกนามสกุลหากไม่มีในรายชื่อ">
                            </div>
                            <div class="col-sm-2 mt-4">
                                <button type="button" class="btn btn-danger btn-sm remove-author">
                                    <i class="mdi mdi-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                $("#authors-container").append(newRow);
                $("#author-select-" + index).select2(); // รีโหลด select2 ให้ dropdown ใช้ได้
                updateAuthorOptions();
            }

            function updateAuthorOptions() {
                var selectedAuthors = new Set();
                $(".author-select").each(function() {
                    var value = $(this).val();
                    if (value) selectedAuthors.add(value);
                });

                $(".author-select").each(function() {
                    var $this = $(this);
                    var currentValue = $this.val();
                    $this.find("option").each(function() {
                        var optionValue = $(this).val();
                        if (selectedAuthors.has(optionValue) && optionValue !== "" && optionValue !== currentValue) {
                            $(this).prop("disabled", true);
                        } else {
                            $(this).prop("disabled", false);
                        }
                    });
                });

                $(".author-select").select2();
            }

            $("#add-author-btn").click(function() {
                appendAuthorRow(authorIndex, "", "", "", true);
                authorIndex++;
            });

            $(document).on("change", ".author-select", function() {
                var index = $(this).data("index");
                var selectedId = $(this).val();
                if (selectedId) {
                    var selectedAuthor = authorsData.find(author => author.id == selectedId);
                    if (selectedAuthor) {
                        $("#author-fname-" + index).val(selectedAuthor.author_fname).prop("disabled", true);
                        $("#author-lname-" + index).val(selectedAuthor.author_lname).prop("disabled", true);
                    }
                } else {
                    $("#author-fname-" + index).val("").prop("disabled", false);
                    $("#author-lname-" + index).val("").prop("disabled", false);
                }
                updateAuthorOptions();
            });

            $(document).on("click", ".remove-author", function() {
                $(this).closest(".author-box").remove();
                updateAuthorOptions();
            });

            // โหลดข้อมูล Author ที่มีอยู่แล้ว
            var authors = @json($researchGroup['author'] ?? []);
            authors.forEach(function(author) {
                appendAuthorRow(authorIndex, author.id, author.author_fname, author.author_lname, false);
                authorIndex++;
            });
        });
    </script>

    {{-- <style>
        /* ทำให้ dropdown ที่ถูกเลือกแล้วเป็นสีดำ */
        select.form-control {
            color: black !important;
        }

        /* สำหรับ Select2 */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: black !important;
        }
    </style> --}}
@stop
