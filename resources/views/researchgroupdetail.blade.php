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
                        <img src="{{ asset('img/banner_research_group.jpg') }}" alt="Group Banner" class="img-fluid w-100">
                        @if (app()->getLocale() == 'en')
                            <h1 class="banner-title"> {{ $rg->group_name_en }} </h1>
                        @elseif (app()->getLocale() == 'th')
                            <h1 class="banner-title"> {{ $rg->group_name_th }} </h1>
                        @endif
                    </div>
                </div>

                {{-- Research Group Info --}}
                <div class="container my-5">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center">
                                @if (app()->getLocale() == 'en')
                                    Research Group Information
                                @elseif (app()->getLocale() == 'th')
                                    ข้อมูลกลุ่มวิจัย
                                @endif
                            </h2>
                            <p class="text-detail mt-4">
                                @if (app()->getLocale() == 'en')
                                    {{ $rg->group_desc_en }}
                                @elseif (app()->getLocale() == 'th')
                                    {{ $rg->group_desc_th }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Research Group Members --}}
                <div class="container my-5">
                    @if ($rg->user->where('research_group_role', 'Head')->isNotEmpty())
                        <div class="mt-5">
                            @if (app()->getLocale() == 'en')
                                <hr>
                                <h1 class="text-center my-4">Head of Research Group</h1>
                            @elseif (app()->getLocale() == 'th')
                                <hr>
                                <h1 class="text-center my-4">หัวหน้ากลุ่มวิจัย</h1>
                            @endif
                            <div class="row justify-content-center">
                                @foreach ($rg->user->where('research_group_role', 'Head') as $r)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="text-center shadow p-4 rounded">
                                            <img src="{{ $r->picture }}" class="card-img-top rounded mx-auto"
                                                style="width: 180px; object-fit: contain;" alt="Profile Image">
                                            <div class="card-body">
                                                <h5 class="fw-bold">
                                                    <p href="#" class="text-decoration-none text-dark">
                                                        @if ($r->doctoral_degree == 'Ph.D.')
                                                            @if (app()->getLocale() == 'en')
                                                                {{ str_replace('Dr.', ' ', $r->{'position_'.app()->getLocale()}) }} {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D.
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @else
                                                            @if (app()->getLocale() == 'en')
                                                                {{ $r->{'position_'.app()->getLocale()} }} {{ $r->fname_en }} {{ $r->lname_en }}
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </h5>
                                                @if (app()->getLocale() == 'en')
                                                    <p class="text-muted">Head of Research Group</p>
                                                @elseif (app()->getLocale() == 'th')
                                                    <p class="text-muted">หัวหน้ากลุ่มวิจัย</p>
                                                @endif
                                                <p><strong>Email:</strong> <span
                                                        class="text-muted">{{ $r->email }}</span></p>
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
                            @if ($rg->user->where('role', 'teacher')->where('research_group_role', 'Member')->isNotEmpty())
                                <h1 class="text-center my-4">
                                    @if (app()->getLocale() == 'en')
                                        Members
                                    @elseif (app()->getLocale() == 'th')
                                        สมาชิคกลุ่มวิจัย
                                    @endif
                                </h1>
                            @endif
                            <div class="row justify-content-center">
                                @foreach ($rg->user->where('research_group_role', 'Member') as $r)
                                    @if ($r->role == 'teacher')
                                        <div class="col-md-6 col-lg-4 my-2">
                                            <div class="text-center shadow p-4">
                                                <img src="{{ $r->picture }}" class="card-img-top rounded mx-auto"
                                                style="width: 180px; object-fit: contain;" alt="Profile Image">
                                                <div class="card-body">
                                                    <h5 class="fw-bold">
                                                        <p class="text-decoration-none text-dark">
                                                            @if ($r->doctoral_degree == 'Ph.D.')
                                                                @if (app()->getLocale() == 'en')
                                                                    {{ str_replace('Dr.', ' ', $r->{'position_'.app()->getLocale()}) }} {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D.
                                                                @elseif (app()->getLocale() == 'th')
                                                                    {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                    {{ $r->lname_th }}
                                                                @endif
                                                            @else
                                                                @if (app()->getLocale() == 'en')
                                                                    {{ $r->{'position_'.app()->getLocale()} }} {{ $r->fname_en }} {{ $r->lname_en }}
                                                                @elseif (app()->getLocale() == 'th')
                                                                    {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                    {{ $r->lname_th }}
                                                                @endif
                                                            @endif
                                                        </p>
                                                    </h5>
                                                    @if (app()->getLocale() == 'en')
                                                        <p class="text-muted">Research Member</p>
                                                    @elseif (app()->getLocale() == 'th')
                                                        <p class="text-muted">สมาชิกกลุ่มวิจัย</p>
                                                    @endif
                                                    <p><strong>Email:</strong> <span
                                                            class="text-muted">{{ $r->email }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-5">
                            @if ($rg->user->where('role', 'student')->isNotEmpty())
                                <hr>
                                <h1 class="text-center my-4">
                                    @if (app()->getLocale() == 'en')
                                        Students
                                    @elseif (app()->getLocale() == 'th')
                                        นักศึกษา
                                    @endif
                                </h1>
                            @endif
                            <div class="row justify-content-center">
                                @foreach ($rg->user->where('research_group_role', 'Member') as $r)
                                    @if ($r->role == 'student')
                                        <div class="col-md-6 col-lg-4 my-2">
                                            <div class="text-center shadow p-4">
                                                <img src="{{ $r->picture }}" class="card-img-top rounded mx-auto"
                                                style="width: 180px; object-fit: contain;" alt="Profile Image">
                                                <div class="card-body">
                                                    <h5 class="fw-bold">
                                                        <p class="text-decoration-none text-dark">
                                                            @if ($r->doctoral_degree == 'Ph.D.')
                                                                @if (app()->getLocale() == 'en')
                                                                    {{ str_replace('Dr.', ' ', $r->{'position_'.app()->getLocale()}) }} {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D.
                                                                @elseif (app()->getLocale() == 'th')
                                                                    {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                    {{ $r->lname_th }}
                                                                @endif
                                                            @else
                                                                @if (app()->getLocale() == 'en')
                                                                    {{ $r->{'position_'.app()->getLocale()} }} {{ $r->fname_en }} {{ $r->lname_en }}
                                                                @elseif (app()->getLocale() == 'th')
                                                                    {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                    {{ $r->lname_th }}
                                                                @endif
                                                            @endif
                                                        </p>
                                                    </h5>
                                                    @if (app()->getLocale() == 'en')
                                                        <p class="text-muted">Research Member</p>
                                                    @elseif (app()->getLocale() == 'th')
                                                        <p class="text-muted">สมาชิกกลุ่มวิจัย</p>
                                                    @endif
                                                    <p><strong>Email:</strong> <span
                                                            class="text-muted">{{ $r->email }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Post-Doc -->
                    @if ($rg->user->where('research_group_role', 'Post-Doc')->isNotEmpty())
                        <div class="mt-5">
                            <hr>
                            <h1 class="text-center my-4">
                                Post-Doctoral Scholars
                            </h1>
                            <div class="row justify-content-center">
                                @foreach ($rg->user->where('research_group_role', 'Post-Doc') as $r)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="text-center shadow p-4">
                                            <img src="{{ $r->picture }}" class="card-img-top rounded mx-auto"
                                                style="width: 180px; object-fit: contain;" alt="Profile Image">
                                            <div class="card-body">
                                                <h5 class="fw-bold">
                                                    <p href="#" class="text-decoration-none text-dark">
                                                        @if ($r->doctoral_degree == 'Ph.D.')
                                                            @if (app()->getLocale() == 'en')
                                                                {{ str_replace('Dr.', ' ', $r->{'position_'.app()->getLocale()}) }} {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D.
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @else
                                                            @if (app()->getLocale() == 'en')
                                                                {{ $r->{'position_'.app()->getLocale()} }} {{ $r->fname_en }} {{ $r->lname_en }}
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </h5>
                                                <p class="text-muted">
                                                    Postdoctoral Researcher
                                                </p>
                                                <p><strong>Email:</strong> <span
                                                        class="text-muted">{{ $r->email }}</span></p>
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
                            <h1 class="text-center my-4">
                                Visiting Scholars
                            </h1>
                            <div class="row justify-content-center">
                                @foreach ($rg->user->where('research_group_role', 'Visitors') as $r)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="text-center shadow p-4">
                                            <img src="{{ $r->picture }}" class="card-img-top rounded mx-auto"
                                                style="width: 180px; object-fit: contain;" alt="Profile Image">
                                            <div class="card-body">
                                                <h5 class="fw-bold">
                                                    <p href="#" class="text-decoration-none text-dark">
                                                        @if ($r->doctoral_degree == 'Ph.D.')
                                                            @if (app()->getLocale() == 'en')
                                                                {{ str_replace('Dr.', ' ', $r->{'position_'.app()->getLocale()}) }} {{ $r->fname_en }} {{ $r->lname_en }}, Ph.D.
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @else
                                                            @if (app()->getLocale() == 'en')
                                                                {{ $r->{'position_'.app()->getLocale()} }} {{ $r->fname_en }} {{ $r->lname_en }}
                                                            @elseif (app()->getLocale() == 'th')
                                                                {{ $r->{'position_' . app()->getLocale()} }}{{ $r->fname_th }}
                                                                {{ $r->lname_th }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </h5>
                                                <p class="text-muted">
                                                    Visitors Researcher
                                                </p>
                                                <p><strong>Email:</strong> <span
                                                        class="text-muted">{{ $r->email }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (count($authors) > 0)
                        <div class="mt-5">
                            <hr>
                            <h1 class="text-center my-4">
                                Other Visiting Scholars
                            </h1>
                            <div class="row justify-content-center">
                                @foreach ($authors as $author)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="text-center shadow p-4">
                                            <div class="card-body">
                                                <h5 class="fw-bold">
                                                    <p href="#" class="text-decoration-none text-dark">
                                                        {{$author->author_fname}} {{$author->author_lname}}
                                                    </p>
                                                </h5>
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
