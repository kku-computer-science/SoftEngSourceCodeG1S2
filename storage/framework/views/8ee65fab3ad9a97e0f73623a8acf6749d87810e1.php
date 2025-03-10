
<?php $__env->startSection('content'); ?>
    <div class="container">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card" style="padding: 16px;">
            <div class="card-body">
                <h4 class="card-title">แก้ไขข้อมูลกลุ่มวิจัย</h4>
                <p class="card-description">กรอกข้อมูลแก้ไขรายละเอียดกลุ่มวิจัย</p>
                <form action="<?php echo e(route('researchGroups.update', $researchGroup->id)); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>ชื่อกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_th" value="<?php echo e($researchGroup->group_name_th); ?>" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (ภาษาไทย)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>ชื่อกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <input name="group_name_en" value="<?php echo e($researchGroup->group_name_en); ?>" class="form-control"
                                placeholder="ชื่อกลุ่มวิจัย (English)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_th" class="form-control" style="height:90px"><?php echo e($researchGroup->group_desc_th); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>คำอธิบายกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_desc_en" class="form-control" style="height:90px"><?php echo e($researchGroup->group_desc_en); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (ภาษาไทย)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_th" class="form-control" style="height:90px"><?php echo e($researchGroup->group_detail_th); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="col-sm-3"><b>รายละเอียดกลุ่มวิจัย (English)</b></p>
                        <div class="col-sm-8">
                            <textarea name="group_detail_en" class="form-control"
                                style="height:90px"><?php echo e($researchGroup->group_detail_en); ?></textarea>
                        </div>
                    </div>
                    
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
                                <?php if(!auth()->user()->hasRole('admin')): ?> disabled <?php endif; ?>>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"
                                        <?php if($researchGroup->user->contains('id', $user->id) && $researchGroup->user->where('id', $user->id)->first()->pivot->role == 1): ?> selected <?php endif; ?>>
                                        <?php echo e($user->fname_th); ?> <?php echo e($user->lname_th); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <a class="btn btn-light mt-5" href="<?php echo e(route('researchGroups.index')); ?>"> Back</a>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('javascript'); ?>
    <script>
        $(document).ready(function() {
            var isAdmin = <?php echo json_encode(auth()->user()->hasRole('admin'), 15, 512) ?>; // ตรวจสอบว่าเป็น admin หรือไม่

            if (!isAdmin) {
                $("#head0").prop("disabled", true); // ถ้าไม่ใช่ admin ปิดการแก้ไข dropdown
            }
        });
        $(document).ready(function() {
            $("#head0").select2();
            
            var isAdmin = <?php echo json_encode(auth()->user()->hasRole('admin'), 15, 512) ?>;
            if (!isAdmin) {
                $("#head0").prop("disabled", true);
            }
            
            var i = 0;
            var researchGroup = <?php echo json_encode($researchGroup['user'] ?? [], 15, 512) ?>;
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
                var userOptions = '<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($user->id); ?>" ' +
                                (userId == "<?php echo e($user->id); ?>" ? "selected" : "") + '><?php echo e($user->fname_th); ?> <?php echo e($user->lname_th); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>';
                var roleOptions = '<option value="2" ' + (roleVal == "2" ? "selected" : "") + '>Member</option>' +
                                '<option value="3" ' + (roleVal == "3" ? "selected" : "") + '>Post-Doc</option>' +
                                '<option value="4" ' + (roleVal == "4" ? "selected" : "") + '>Visitor</option>';

                var permissionOptions = isAdmin ? 
                    '<select name="moreFields[users][' + index + '][permission]" class="form-control">' +
                        '<option value="2" ' + (permissionVal == "2" ? "selected" : "") + '>View</option>' +
                        '<option value="1" ' + (permissionVal == "1" ? "selected" : "") + '>Edit</option>' +
                    '</select>' :
                    '<input type="text" class="form-control" value="' + (permissionVal == "1" ? "Edit" : "View") + '" readonly>' +
                    '<input type="hidden" name="moreFields[users][' + index + '][permission]" value="' + permissionVal + '">';

                var newRow = '<tr>' +
                    '<td><select id="selUser' + index + '" name="moreFields[users][' + index + '][userid]" class="member-select form-control" style="width: 200px;">' +
                    '<option value="">Select User</option>' + userOptions + '</select></td>' +
                    '<td><select name="moreFields[users][' + index + '][role]" class="form-control">' + roleOptions + '</select></td>' +
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
            var authors = <?php echo json_encode($researchGroup['author'] ?? [], 15, 512) ?>;
            authors.forEach(function(author) {
                appendAuthorRow(j, author.id);
                j++;
            });

            $("#add-btn2-authors").click(function() {
                appendAuthorRow(j, "");
                j++;
            });

            function appendAuthorRow(index, authorId) {
                var authorOptions = '<?php $__currentLoopData = $authors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $author): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($author->id); ?>"><?php echo e($author->author_fname); ?> <?php echo e($author->author_lname); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>';
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dashboards.users.layouts.user-dash-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/research_groups/edit.blade.php ENDPATH**/ ?>