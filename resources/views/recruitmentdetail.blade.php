@extends('layouts.layout')

<style>
    /* Main color palette */
    :root {
        --primary-blue: #1a73e8;
        --light-blue: #e8f0fe;
        --dark-blue: #174ea6;
        --text-primary: #202124;
        --text-secondary: #5f6368;
        --bg-light: #f8f9fa;
        --divider: #dadce0;
    }

    .banner-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .banner-container img {
        display: block;
        width: 100%;
        height: auto;
        filter: brightness(80%);
        /* ปรับค่าความมืดของรูป (ค่าต่ำกว่าปกติ 100%) */
    }

    /* หรือถ้าต้องการให้ปรับความมืดแบบ Overlay */
    .banner-container::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* เลเยอร์ดำครึ่งโปร่งใส */
    }

    .banner-title {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 3.8rem;
        font-weight: bold;
        text-align: center;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        z-index: 2;
        /* ทำให้ตัวหนังสืออยู่ด้านหน้าสุด */
    }


    /* Content container */
    .recruitment-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Main heading */
    .position-heading {
        text-align: center;
        /* font-size: 1.75rem;
        font-weight: 500; */
        margin-bottom: 30px;
        color: #000;
        position: relative;
        padding-bottom: 15px;
    }

    .position-heading::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background-color: #000;
        border-radius: 3px;
    }

    /* Card styling */
    .details-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 30px;
        margin-bottom: 30px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        word-wrap: break-word;

    }

    .details-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Card header */
    .details-header {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--divider);
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Section titles */
    .info-heading {
        font-size: 1.2rem;
        font-weight: 500;
        margin: 25px 0 10px;
        color: #000;
        position: relative;
        display: inline-block;
        padding-left: 15px;
    }

    .info-heading::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: #000;
        border-radius: 2px;
    }

    /* Section content */
    .info-content {
        margin-bottom: 20px;
        font-size: 1rem;
        color: var(--text-primary);
        line-height: 1.6;
        padding-left: 30px;
    }

    /* List styling */
    .details-card ul {
        list-style-type: none;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .details-card ul li {
        font-size: 1rem;
        margin-bottom: 8px;
        color: var(--text-primary);
        line-height: 1.6;
        position: relative;
        padding-left: 25px;
    }

    .details-card ul li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #000;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Divider */
    .section-divider {
        border-top: 1px solid var(--divider);
        margin: 25px 0;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        background-color: var(--bg-light);
        border-radius: 12px;
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .banner-title {
            font-size: 2rem;
        }
        
        .position-heading {
            font-size: 1.5rem;
        }
        
        .details-card {
            padding: 20px;
        }
    }
</style>

@section('content')
    <div class="banner-container">
        <img src="{{ asset('img/banner_research_group.jpg') }}" alt="Group Banner">
        @if (app()->getLocale() == 'en')
            <h1 class="banner-title">{{ $recruitment->researchGroup->group_name_en }}</h1>
        @elseif (app()->getLocale() == 'th')
            <h1 class="banner-title">{{ $recruitment->researchGroup->group_name_th }}</h1>
        @endif
    </div>

    <div class="recruitment-container">
        <h2 class="position-heading">
            @if (app()->getLocale() == 'en')
                {{ $recruitment->title_en }}
            @elseif (app()->getLocale() == 'th')
                {{ $recruitment->title_th }}
            @endif
        </h2>
        
        <div class="details-card">
            <div class="details-header">
                @if (app()->getLocale() == 'en')
                    Recruitment Details
                @elseif (app()->getLocale() == 'th')
                    ข้อมูลรายละเอียดประกาศรับสมัคร
                @endif
            </div>
            
            <h3 class="info-heading">
                @if (app()->getLocale() == 'en')
                    Position
                @elseif (app()->getLocale() == 'th')
                    ตำแหน่ง
                @endif
            </h3>
            <div class="info-content">
                @if (app()->getLocale() == 'en')
                    {{ $position->name_en }}
                @elseif (app()->getLocale() == 'th')
                    {{ $position->name_th }}
                @endif
            </div>
            
            <h3 class="info-heading">
                @if (app()->getLocale() == 'en')
                    Job Description
                @elseif (app()->getLocale() == 'th')
                    รายละเอียดงาน
                @endif
            </h3>
            <div class="info-content">
                @if (app()->getLocale() == 'en')
                    {{ $recruitment->job_description_en }}
                @elseif (app()->getLocale() == 'th')
                    {{ $recruitment->job_description_th }}
                @endif
            </div>
            
            <h3 class="info-heading">
                @if (app()->getLocale() == 'en')
                    Qualifications
                @elseif (app()->getLocale() == 'th')
                    คุณสมบัติ
                @endif
            </h3>
            <ul>
                @foreach($qualifications as $quali)
                    <li>
                        @if (app()->getLocale() == 'en')
                            {{ $quali->text_en }}
                        @elseif (app()->getLocale() == 'th')
                            {{ $quali->text_th }}
                        @endif
                    </li>
                @endforeach
            </ul>
            
            @if (!empty($recruitment->other_en))
                <h3 class="info-heading">
                    @if (app()->getLocale() == 'en')
                        Other Details
                    @elseif (app()->getLocale() == 'th')
                        รายละเอียดเพิ่มเติม
                    @endif
                </h3>
                <div class="info-content">
                    @if (app()->getLocale() == 'en')
                        {{ $recruitment->other_en }}
                    @elseif (app()->getLocale() == 'th')
                        {{ $recruitment->other_th }}
                    @endif
                </div>
            @endif
            
            <h3 class="info-heading">
                @if (app()->getLocale() == 'en')
                    Place
                @elseif (app()->getLocale() == 'th')
                    สถานที่ทำงาน
                @endif
            </h3>
            <div class="info-content">
                @if (app()->getLocale() == 'en')
                    {{ $recruitment->place_en }}
                @elseif (app()->getLocale() == 'th')
                    {{ $recruitment->place_th }}
                @endif
            </div>
            
            @if (!empty($recruitment->other_en))
                <h3 class="info-heading">
                    @if (app()->getLocale() == 'en')
                        Salary
                    @elseif (app()->getLocale() == 'th')
                        ค่าตอบแทน
                    @endif
                </h3>
                <div class="info-content">
                    @if (app()->getLocale() == 'en')
                        {{ $recruitment->salary }} Baht
                    @elseif (app()->getLocale() == 'th')
                        {{ $recruitment->salary }} บาท
                    @endif
                </div>
            @endif
            
            <h3 class="info-heading">
                @if (app()->getLocale() == 'en')
                    Contact
                @elseif (app()->getLocale() == 'th')
                    ช่องทางการสมัครงาน
                @endif
            </h3>
            <div class="info-content">
                @if (app()-> getLocale() == 'en')
                    {{ $recruitment->apply_channel_en }}
                @elseif (app()->getLocale() == 'th')
                    {{ $recruitment->apply_channel_th }}
                @endif


            </div>
        </div>
    </div>
@endsection