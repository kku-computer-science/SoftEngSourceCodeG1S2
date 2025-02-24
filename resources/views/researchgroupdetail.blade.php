@extends('layouts.layout')

<style>
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


    .name {
        font-size: 20px;
    }

    .role-section {
        width: 80%;
        margin-top: 40px;
        margin: 0 auto;
    }

    .role-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .members-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .member-card {
        width: 220px;
        text-align: center;
    }

    .member-card img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .text-detail {
        font-size: 1.3rem;
    }
</style>

@section('content')
<div class="card-4">
    <div class="">
        @foreach ($resgd as $index => $rg)
        <div class="text-center mb-4">
            <!-- ทำให้ภาพแรกเป็นแบนเนอร์ -->

            <div class="banner-container">
                <img src="{{ asset('img/banner_research_group.jpg') }}"
                    alt="Group Banner"
                    class="img-fluid w-100">
                <h1 class="banner-title"> {{ $rg->group_name_en }} </h1>
            </div>
        </div>

        <div class="container my-5">
            <!-- Laboratory Supervisor -->
            @if ($rg->user->where('research_group_role', 'Head')->isNotEmpty())
            <div class="mt-5">
                <h1 class="text-center my-4">Laboratory Supervisor</h1>
                <div class="row justify-content-center">
                    @foreach ($rg->user->where('research_group_role', 'Head') as $r)
                    <div class="col-md-6 col-lg-4">
                        <div class="text-center shadow p-4 rounded">
                            <img src="{{ $r->picture }}" class="card-img-top rounded-circle mx-auto"
                                style="width: 180px; height: 180px; object-fit: cover;" alt="Profile Image">
                            <div class="card-body">
                                <h5 class="fw-bold">
                                    <p href="#" class="text-decoration-none text-dark">
                                        Prof. {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D
                                    </p>
                                </h5>
                                <p class="text-muted">Head of Research Group</p>
                                <p><strong>Email:</strong> <span class="text-muted">{{ $r->email }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Members -->
            @if ($rg->user->where('research_group_role', 'Member')->isNotEmpty())
            <div class="mt-5">
                <hr>
                <h1 class="text-center my-4">Members</h1>
                <div class="row justify-content-center">
                    @foreach ($rg->user->where('research_group_role', 'Member') as $r)
                    <div class="col-md-6 col-lg-4 my-2">
                        <div class="text-center shadow p-4">
                            <img src="{{ $r->picture }}" class="card-img-top rounded-circle mx-auto"
                                style="width: 180px; height: 180px; object-fit: cover;" alt="Profile Image">
                            <div class="card-body">
                                <h5 class="fw-bold">
                                    <p class="text-decoration-none text-dark">
                                        {{ $r->fname_en }} {{ $r->lname_en }}
                                    </p>
                                </h5>
                                <p class="text-muted">Research Member</p>
                                <p><strong>Email:</strong> <span class="text-muted">{{ $r->email }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Post-Doc -->
            @if ($rg->user->where('research_group_role', 'Post-Doc')->isNotEmpty())
            <div class="mt-5">
                <hr>
                <h1 class="text-center my-4">Post-Doc</h1>
                <div class="row justify-content-center">
                    @foreach ($rg->user->where('research_group_role', 'Post-Doc') as $r)
                    <div class="col-md-6 col-lg-4">
                        <div class="text-center shadow p-4">
                            <img src="{{ $r->picture }}" class="card-img-top rounded-circle mx-auto"
                                style="width: 180px; height: 180px; object-fit: cover;" alt="Profile Image">
                            <div class="card-body">
                                <h5 class="fw-bold">
                                    <p href="#" class="text-decoration-none text-dark">
                                        {{ $r->fname_en }} {{ $r->lname_en }}
                                    </p>
                                </h5>
                                <p class="text-muted">Postdoctoral Researcher</p>
                                <p><strong>Email:</strong> <span class="text-muted">{{ $r->email }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Visitors -->
            @if ($rg->user->where('research_group_role', 'Visitors')->isNotEmpty())
            <div class="mt-5">
                <hr>
                <h1 class="text-center my-4">Visitors</h1>
                <div class="row justify-content-center">
                    @foreach ($rg->user->where('research_group_role', 'Visitors') as $r)
                    <div class="col-md-6 col-lg-4">
                        <div class="text-center shadow p-4">
                            <img src="{{ $r->picture }}" class="card-img-top rounded-circle mx-auto"
                                style="width: 180px; height: 180px; object-fit: cover;" alt="Profile Image">
                            <div class="card-body">
                                <h5 class="fw-bold">
                                    <p href="#" class="text-decoration-none text-dark">
                                        {{ $r->fname_en }} {{ $r->lname_en }}
                                    </p>
                                </h5>
                                <p class="text-muted">Visitors Researcher</p>
                                <p><strong>Email:</strong> <span class="text-muted">{{ $r->email }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>



        @endforeach
    </div>
</div>
@stop


<!-- <div class="card-body-research">
                    <p>Research</p>
                    <table class="table">
                        @foreach($rg->user as $user)
                        
                        <thead>
                            <tr>
                                <th><b class="name">{{$user->{'position_'.app()->getLocale()} }} {{$user->{'fname_'.app()->getLocale()} }} {{$user->{'lname_'.app()->getLocale()} }}</b></th>
                            </tr>
                            @foreach($user->paper->sortByDesc('paper_yearpub') as $p)
                            <tr class="hidden">
                                <th>
                                    <b><math>{!! html_entity_decode(preg_replace('<inf>', 'sub', $p->paper_name)) !!}</math></b> (
                                    <link>@foreach($p->teacher as $teacher){{$teacher->fname_en}} {{$teacher->lname_en}},
                                    @endforeach
                                    @foreach($p->author as $author){{$author->author_fname}} {{$author->author_lname}}@if (!$loop->last),@endif
                                    @endforeach</link>), {{$p->paper_sourcetitle}}, {{$p->paper_volume}},
                                    {{ $p->paper_yearpub }}.
                                    <a href="{{$p->paper_url}} " target="_blank">[url]</a> <a href="https://doi.org/{{$p->paper_doi}}" target="_blank">[doi]</a>
                                </th>
                            </tr>
                            @endforeach
                        </thead>
                        @endforeach
                    </table>
                </div> -->