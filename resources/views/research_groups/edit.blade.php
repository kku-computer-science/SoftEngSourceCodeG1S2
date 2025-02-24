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

                    @if(auth()->user()->is_admin)
                    <div class="form-group row">
                        <p class="col-sm-3"><b>หัวหน้ากลุ่มวิจัย</b></p>
                        <div class="col-sm-8">
                            <select id='head0' name="head" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        @if ($researchGroup->user->contains('id', $user->id) && $researchGroup->user->where('id', $user->id)->first()->pivot->role == 1) selected @endif>
                                        {{ $user->fname_th }} {{ $user->lname_th }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

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
                    </div>
                    <button type="submit" class="btn btn-primary mt-5">Submit</button>
                    <a class="btn btn-light mt-5" href="{{ route('researchGroups.index') }}"> Back</a>
                </form>
            </div>
        </div>
    </div>
@stop
