@extends('dashboards.users.layouts.user-dash-layout')

@section('content')
<div class="container">
    <div class="card col-md-10" style="padding: 16px;">
        <div class="card-body">
            <h4 class="card-title">รายละเอียดประกาศรับสมัคร</h4>
            <p class="card-description">ข้อมูลรายละเอียดประกาศรับสมัคร</p>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ชื่อกลุ่มวิจัย</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->researchGroup->group_name_th }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ประกาศรับสมัคร (ภาษาไทย)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->title_th }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ประกาศรับสมัคร (English)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->title_en }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ตำแหน่ง</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->position->name_en }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>รายละเอียดงาน (ภาษาไทย)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->job_description_th }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>รายละเอียดงาน (English)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->job_description_en }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>คุณสมบัติ (ภาษาไทย)</b></p>
                <ul class="card-text col-sm-9">
                    @foreach($recruitment->qualifications as $qualification)
                        <li>{{ $qualification->text_th }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>คุณสมบัติ (English)</b></p>
                <ul class="card-text col-sm-9">
                    @foreach($recruitment->qualifications as $qualification)
                        <li>{{ $qualification->text_en }}</li>
                    @endforeach
                </ul>
            </div>
            @if($recruitment->other_th)
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>รายละเอียดเพิ่มเติม (ภาษาไทย)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->other_th }}</p>
            </div>
            @endif
            @if($recruitment->other_en)
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>รายละเอียดเพิ่มเติม (English)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->other_en }}</p>
            </div>
            @endif
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>สถานที่ทำงาน (ภาษาไทย)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->place_th }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>สถานที่ทำงาน (English)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->place_en }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ค่าตอบแทน</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->salary }} บาท</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ช่องทางการสมัคร (ภาษาไทย)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->apply_channel_th }}</p>
            </div>
            <div class="row mt-2">
                <p class="card-text col-sm-3"><b>ช่องทางการสมัคร (English)</b></p>
                <p class="card-text col-sm-9">{{ $recruitment->apply_channel_en }}</p>
            </div>
            <a href="{{ route('recruitment.index') }}" class="btn btn-primary mt-3">Back</a>   
@endsection