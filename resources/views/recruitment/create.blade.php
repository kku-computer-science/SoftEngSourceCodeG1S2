@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">สร้างประกาศรับสมัครงาน</h2>
    
    <form action="{{ route('recruitments.store') }}" method="POST">
        @csrf

        <!-- กลุ่มวิจัย (เลือกได้เฉพาะกลุ่มของตัวเอง) -->
        <div class="form-group">
            <label for="research_group_id">กลุ่มวิจัย *</label>
            <select class="form-control" id="research_group_id" name="research_group_id" required>
                <option value="">-- เลือกกลุ่มวิจัย --</option>
                @foreach($researchGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- ชื่อประกาศรับสมัคร -->
        <div class="form-group">
            <label for="title_th">ชื่อประกาศรับสมัคร (ภาษาไทย) *</label>
            <input type="text" class="form-control" id="title_th" name="title_th" required>
        </div>
        
        <div class="form-group">
            <label for="title_en">ชื่อประกาศรับสมัคร (English) *</label>
            <input type="text" class="form-control" id="title_en" name="title_en" required>
        </div>

        <!-- ตำแหน่ง (ดึงจาก Table recruitment_position) -->
        <div class="form-group">
            <label for="position_id">ตำแหน่ง *</label>
            <select class="form-control" id="position_id" name="position_id" required>
                <option value="">-- เลือกตำแหน่ง --</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name_th }} ({{ $position->name_en }})</option>
                @endforeach
            </select>
        </div>

        <!-- รายละเอียดงาน -->
        <div class="form-group">
            <label for="job_description_th">รายละเอียดงาน (ภาษาไทย) *</label>
            <textarea class="form-control" id="job_description_th" name="job_description_th" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="job_description_en">รายละเอียดงาน (English) *</label>
            <textarea class="form-control" id="job_description_en" name="job_description_en" rows="4" required></textarea>
        </div>

        <!-- คุณสมบัติ -->
        <div class="form-group">
            <label>คุณสมบัติ *</label>
            <div id="qualification-container">
                <div class="d-flex mb-2">
                    <input type="text" class="form-control mr-2" name="qualifications[0][text_th]" placeholder="ภาษาไทย" required>
                    <input type="text" class="form-control mr-2" name="qualifications[0][text_en]" placeholder="English" required>
                    <button type="button" class="btn btn-success add-qualification">+</button>
                </div>
            </div>
        </div>

        <!-- รายละเอียดเพิ่มเติม -->
        <div class="form-group">
            <label for="other_th">รายละเอียดเพิ่มเติม (ภาษาไทย)</label>
            <textarea class="form-control" id="other_th" name="other_th" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label for="other_en">รายละเอียดเพิ่มเติม (English)</label>
            <textarea class="form-control" id="other_en" name="other_en" rows="2"></textarea>
        </div>

        <!-- สถานที่ทำงาน -->
        <div class="form-group">
            <label for="place_th">สถานที่ทำงาน (ภาษาไทย) *</label>
            <input type="text" class="form-control" id="place_th" name="place_th" required>
        </div>

        <div class="form-group">
            <label for="place_en">สถานที่ทำงาน (English) *</label>
            <input type="text" class="form-control" id="place_en" name="place_en" required>
        </div>

        <!-- ค่าตอบแทน -->
        <div class="form-group">
            <label for="salary">ค่าตอบแทน *</label>
            <input type="number" class="form-control" id="salary" name="salary" min="0" required>
        </div>

        <!-- ช่องทางการสมัคร -->
        <div class="form-group">
            <label for="apply_channel">ช่องทางการสมัคร (ภาษาไทย) *</label>
            <input type="text" class="form-control" id="apply_channel" name="apply_channel" required>
        </div>

        <div class="form-group">
            <label for="apply_channel_en">ช่องทางการสมัคร (English) *</label>
            <input type="text" class="form-control" id="apply_channel_en" name="apply_channel_en" required>
        </div>

        <!-- ปุ่ม Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{ route('recruitments.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>

<!-- JavaScript สำหรับเพิ่ม/ลบคุณสมบัติ -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let qualificationIndex = 1;

        document.querySelector('.add-qualification').addEventListener('click', function () {
            const container = document.getElementById('qualification-container');
            const div = document.createElement('div');
            div.classList.add('d-flex', 'mb-2');

            div.innerHTML = `
                <input type="text" class="form-control mr-2" name="qualifications[${qualificationIndex}][text_th]" placeholder="ภาษาไทย" required>
                <input type="text" class="form-control mr-2" name="qualifications[${qualificationIndex}][text_en]" placeholder="English" required>
                <button type="button" class="btn btn-danger remove-qualification">-</button>
            `;

            container.appendChild(div);

            div.querySelector('.remove-qualification').addEventListener('click', function () {
                div.remove();
            });

            qualificationIndex++;
        });
    });
</script>

@endsection
