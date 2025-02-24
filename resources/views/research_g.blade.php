@extends('layouts.layout')
@section('content')

<style>
/* ปรับการ์ดให้เป็นลิงก์ */
.card-link {
    text-decoration: none; /* ลบขีดเส้นใต้ */
    color: inherit; /* ใช้สีเดิม */
    display: block;
    transition: transform 0.3s ease-in-out;
}

/* ปรับขนาดการ์ด */
.research-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease-in-out;
}

/* Container ของรูป */
.image-container {
    position: relative;
    width: 100%;
    height: 220px; /* ปรับขนาดรูปให้เท่ากัน */
    overflow: hidden;
    border-radius: 15px;
}

/* รูปภาพในการ์ด */
.fixed-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* ป้องกันการผิดสัดส่วน */
    transition: transform 0.3s ease-in-out;
}

/* Overlay ของชื่อแล็บ (เริ่มต้นซ่อน) */
.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7); /* สีดำโปร่งแสง */
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

/* ชื่อแล็บ */
.lab-title {
    color: white;
    font-weight: bold;
    font-size: 18px;
    text-align: center;
}

/* Hover Effect */
.image-container:hover .overlay {
    opacity: 1;
}

.image-container:hover .fixed-image {
    transform: scale(1.1); /* ขยายภาพเล็กน้อย */
}

.research-card:hover {
    transform: translateY(-5px); /* ยกการ์ดขึ้นเล็กน้อย */
}
</style>



<div class="container mt-4">
    <h1 class="text-center mb-4">Research Groups</h1>

    <div class="row row-cols-1 row-cols-md-3 g-3 ">
        @foreach ($resg as $rg)
        <div class="col">
            <!-- การ์ดเป็นลิงก์ -->
            <a href="{{ route('researchgroupdetail',['id'=>$rg->id]) }}" class="card-link">
                <div class="card research-card shadow-sm border-0">
                    
                    <!-- ส่วนรูปภาพ -->
                    <div class="image-container">
                        <img src="{{ asset('img/'.$rg->group_image) }}" class="card-img-top fixed-image" alt="Group Image">
                        
                        <!-- Overlay เมื่อ Hover -->
                        <div class="overlay">
                            <div class="lab-title">{{ $rg->{'group_name_'.app()->getLocale()} }}</div>
                        </div>
                    </div>

                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

@stop
