@extends('dashboards.users.layouts.user-dash-layout')

@section('content')
<div class="card" style="padding: 16px;">
    <div class="card-body">
        <h2 class="card-title mb-4">แก้ไขประกาศรับสมัครงาน</h2>

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
                <textarea class="form-control job-description" id="job_description_th" name="job_description_th" required>{{ $recruitment->job_description_th }}</textarea>
            </div>

            <div class="form-group">
                <label for="job_description_en">รายละเอียดงาน (English) *</label>
                <textarea class="form-control job-description" id="job_description_en" name="job_description_en" required>{{ $recruitment->job_description_en }}</textarea>
            </div>

            <!-- คุณสมบัติ -->
            <div class="form-group d-flex justify-content-between align-items-center">
                <label class="mb-0">คุณสมบัติ *</label>
                <button type="button" class="btn btn-success add-qualification">+</button>
            </div>

            <div id="qualification-container">
                @foreach($recruitment->qualifications as $index => $qualification)
                    <div class="d-flex mb-2 justify-content-between align-items-center qualification-row">
                        <div class="w-100">
                            <div class="mb-2">
                                <input type="text" class="form-control qualification-input" name="qualifications[{{ $index }}][text_th]" value="{{ $qualification->text_th }}" placeholder="ภาษาไทย" required>
                            </div>
                            <div class="mb-2  qualification-border">
                                <input type="text" class="form-control qualification-input" name="qualifications[{{ $index }}][text_en]" value="{{ $qualification->text_en }}" placeholder="English" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger remove-qualification">-</button>
                    </div>
                @endforeach
            </div>

            <!-- รายละเอียดเพิ่มเติม -->
            <div class="form-group">
                <label for="other_th">รายละเอียดเพิ่มเติม (ภาษาไทย)</label>
                <textarea class="form-control extra-detail" id="other_th" name="other_th">{{ $recruitment->other_th }}</textarea>
            </div>

            <div class="form-group">
                <label for="other_en">รายละเอียดเพิ่มเติม (English)</label>
                <textarea class="form-control extra-detail" id="other_en" name="other_en">{{ $recruitment->other_en }}</textarea>
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
                <input type="number" class="form-control" id="salary" name="salary" value="{{ $recruitment->salary }}" min="0" placeholder="บาท" >
            </div>

            <!-- ช่องทางการสมัคร -->
            <div class="form-group">
                <label for="apply_channel_th">ช่องทางการสมัคร (ภาษาไทย) *</label>
                <input type="text" class="form-control" id="apply_channel_th" name="apply_channel_th" value="{{ $recruitment->apply_channel_th }}" required>
            </div>

            <div class="form-group">
                <label for="apply_channel_en">ช่องทางการสมัคร (English) *</label>
                <input type="text" class="form-control" id="apply_channel_en" name="apply_channel_en" value="{{ $recruitment->apply_channel_en }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('recruitment.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<!-- JavaScript สำหรับเพิ่ม/ลบคุณสมบัติ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.add-qualification').addEventListener('click', function () {
        const container = document.getElementById('qualification-container');
        const index = container.children.length;

        const mainDiv = document.createElement('div');
        mainDiv.classList.add('d-flex', 'mb-2', 'justify-content-between', 'align-items-center', 'qualification-row');

        const inputDiv = document.createElement('div');
        inputDiv.classList.add('w-100');
        inputDiv.innerHTML = `
            <div class="mb-2">
                <input type="text" class="form-control qualification-input" name="qualifications[${index}][text_th]" placeholder="ภาษาไทย" required>
            </div>
            <div class="mb-2  qualification-border">
                <input type="text" class="form-control qualification-input" name="qualifications[${index}][text_en]" placeholder="English" required>
            </div>
        `;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.classList.add('btn', 'btn-danger', 'remove-qualification');
        removeButton.innerHTML = '-';
        removeButton.addEventListener('click', function () { mainDiv.remove(); });

        mainDiv.appendChild(inputDiv);
        mainDiv.appendChild(removeButton);
        container.appendChild(mainDiv);
    });
});
</script>

<!-- CSS -->
<style>
    .job-description {
        height: 120px; /* กำหนดความสูงของ textarea รายละเอียดงาน */
        
    }

    .extra-detail {
        height: 100px; /* กำหนดความสูงของ textarea รายละเอียดเพิ่มเติม */
    }

    .form-group label {
        font-weight: bold;
    }
    
    .form-group.d-flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    }

    .qualification-input {
        width: 400px;
        margin-right: 1rem;
    }

    .qualification-border {
        border-bottom: 1.5px solid #dddcdc;
        margin-bottom: 1rem;
        padding-bottom: 5px;
    }

    .add-qualification {
        width: 40px;
        height: 40px;
        font-size: 25px;
        text-align: center;
    }

    .btn-success, .btn-danger {
    width: 40px;
    height: 40px;
    padding: 5px;
    text-align: center;
    font-size: 25px;
    }
</style>

@endsection
