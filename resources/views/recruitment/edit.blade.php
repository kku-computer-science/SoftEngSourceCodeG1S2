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
            <h4 class="card-title">แก้ไขประกาศรับสมัครงาน</h4>
            <p class="card-description">กรอกข้อมูลแก้ไขรายละเอียดประกาศรับสมัครงาน</p>
            
            <form action="{{ route('recruitment.update', $recruitment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- กลุ่มวิจัย -->
                <div class="form-group">
                    <label for="research_group_id">กลุ่มวิจัย *</label>
                    <select class="form-control" id="research_group_id" name="research_group_id" required>
                        <option value="">-- เลือกกลุ่มวิจัย --</option>
                        @foreach($researchGroups as $group)
                            <option value="{{ $group->id }}" {{ $recruitment->research_group_id == $group->id ? 'selected' : '' }}>
                                {{ $group->group_name_th }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ชื่อประกาศรับสมัคร -->
                <div class="form-group">
                    <label for="title_th">ชื่อประกาศรับสมัคร (ภาษาไทย) *</label>
                    <input type="text" class="form-control" id="title_th" name="title_th" value="{{ $recruitment->title_th }}" required>
                </div>
                
                <div class="form-group">
                    <label for="title_en">ชื่อประกาศรับสมัคร (English) *</label>
                    <input type="text" class="form-control" id="title_en" name="title_en" value="{{ $recruitment->title_en }}" required>
                </div>

                <!-- ตำแหน่ง -->
                <div class="form-group">
                    <label for="position_id">ตำแหน่ง *</label>
                    <select class="form-control" id="position_id" name="position_id" required>
                        <option value="">-- เลือกตำแหน่ง --</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ $recruitment->position_id == $position->id ? 'selected' : '' }}>
                                {{ $position->name_th }} ({{ $position->name_en }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- รายละเอียดงาน -->
                <div class="form-group">
                    <label for="job_description_th">รายละเอียดงาน (ภาษาไทย) *</label>
                    <textarea class="form-control" id="job_description_th" name="job_description_th" rows="4" required>{{ $recruitment->job_description_th }}</textarea>
                </div>

                <div class="form-group">
                    <label for="job_description_en">รายละเอียดงาน (English) *</label>
                    <textarea class="form-control" id="job_description_en" name="job_description_en" rows="4" required>{{ $recruitment->job_description_en }}</textarea>
                </div>

                <!-- คุณสมบัติ -->
                <div class="form-group">
                    <label>คุณสมบัติ *</label>
                    <div id="qualification-container">
                        @foreach($recruitment->qualifications as $index => $qualification)
                            <div class="d-flex mb-2">
                                <input type="text" class="form-control mr-2" name="qualifications[{{ $index }}][text_th]" value="{{ $qualification->text_th }}" placeholder="ภาษาไทย" required>
                                <input type="text" class="form-control mr-2" name="qualifications[{{ $index }}][text_en]" value="{{ $qualification->text_en }}" placeholder="English" required>
                                <button type="button" class="btn btn-danger remove-qualification">-</button>
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-success add-qualification">+</button>
                    </div>
                </div>

                <!-- รายละเอียดเพิ่มเติม -->
                <div class="form-group">
                    <label for="other_th">รายละเอียดเพิ่มเติม (ภาษาไทย)</label>
                    <textarea class="form-control" id="other_th" name="other_th" rows="2">{{ $recruitment->other_th }}</textarea>
                </div>

                <div class="form-group">
                    <label for="other_en">รายละเอียดเพิ่มเติม (English)</label>
                    <textarea class="form-control" id="other_en" name="other_en" rows="2">{{ $recruitment->other_en }}</textarea>
                </div>

                <!-- สถานที่ทำงาน -->
                <div class="form-group">
                    <label for="place_th">สถานที่ทำงาน (ภาษาไทย) *</label>
                    <input type="text" class="form-control" id="place_th" name="place_th" value="{{ $recruitment->place_th }}" required>
                </div>

                <div class="form-group">
                    <label for="place_en">สถานที่ทำงาน (English) *</label>
                    <input type="text" class="form-control" id="place_en" name="place_en" value="{{ $recruitment->place_en }}" required>
                </div>

                <!-- ค่าตอบแทน -->
                <div class="form-group">
                    <label for="salary">ค่าตอบแทน *</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="{{ $recruitment->salary }}" min="0" required>
                </div>

                <!-- ช่องทางการสมัคร -->
                <div class="form-group">
                    <label for="apply_channel">ช่องทางการสมัคร (ภาษาไทย) *</label>
                    <input type="text" class="form-control" id="apply_channel_th" name="apply_channel_th" value="{{ $recruitment->apply_channel_th }}" required>
                </div>
                <div class="form-group">
                    <label for="apply_channel">ช่องทางการสมัคร (อังกฤษ) *</label>
                    <input type="text" class="form-control" id="apply_channel_en" name="apply_channel_en" value="{{ $recruitment->apply_channel_en }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('recruitment.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript สำหรับเพิ่ม/ลบคุณสมบัติ -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.add-qualification').addEventListener('click', function () {
            const container = document.getElementById('qualification-container');
            const index = container.children.length - 1; // หาจำนวนคุณสมบัติที่มีอยู่

            const div = document.createElement('div');
            div.classList.add('d-flex', 'mb-2');
            div.innerHTML = `
                <input type="text" class="form-control mr-2" name="qualifications[${index}][text_th]" placeholder="ภาษาไทย" required>
                <input type="text" class="form-control mr-2" name="qualifications[${index}][text_en]" placeholder="English" required>
                <button type="button" class="btn btn-danger remove-qualification">-</button>
            `;

            container.insertBefore(div, this);
            div.querySelector('.remove-qualification').addEventListener('click', function () {
                div.remove();
            });
        });

        document.querySelectorAll('.remove-qualification').forEach(button => {
            button.addEventListener('click', function () {
                this.parentElement.remove();
            });
        });
    });
</script>

@endsection
