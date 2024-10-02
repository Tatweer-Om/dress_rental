<!doctype html>
<html lang="en">


<!-- Mirrored from themesbrand.com/minia/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 05 Aug 2024 05:37:11 GMT -->
<head>

        <meta charset="utf-8" />
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico')}}">

        <!-- preloader css -->
        <link rel="stylesheet" href="{{ asset('css/preloader.min.css')}}" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="{{ asset('css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.css')}}">

    </head>

    <body>

    <!-- <body data-layout="horizontal"> -->
        <div class="auth-page">
            @if(session('error'))
                <div class="alert alert-danger" id="error-alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>

            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-xxl-3 col-lg-4 col-md-5">
                        <div class="auth-full-page-content d-flex p-sm-5 p-4">
                            <div class="w-100">
                                <div class="d-flex flex-column h-100">

                                    <div class="auth-content my-auto">
                                        <div class="text-center">
                                            <div class="mb-4 mb-md-5 text-center">
                                                <a href="index.html" class="d-block auth-logo">
                                                    <img src="" alt="" height="28"> <span class="logo-txt">{{ $about->about_name ?? '' }}</span>
                                                </a>
                                            </div>
                                            <h5 class="mb-0">مرحبًا بكم في النظام</h5>
                                            <p class="text-muted mt-2">قم بتسجيل الدخول باستخدام اسم المستخدم وكلمة المرور.</p>
                                        </div>
                                        <form id="loginForm" class="mt-4 pt-2 login_user" method="POST" >
                                            @csrf
                                            <div class="mb-3">
                                                <label for="username" class="form-label">User Name or Email </label>
                                                <input type="text" class="form-control" id="username" name="email" placeholder="أدخل اسم المستخدم" autocomplete="username" >
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <label for="password" class="form-label">كلمة المرور</label>
                                                    </div>
                                                </div>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="أدخل كلمة المرور" autocomplete="current-password" >
                                                    <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">تسجيل الدخول</button>
                                            </div>
                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- end auth full page content -->
                    </div>
                    <!-- end col -->
                    <div class="col-xxl-9 col-lg-8 col-md-7">
                        <div class="auth-bg pt-md-5 p-4 d-flex">
                            <div class="bg-overlay bg-primary"></div>
                            <ul class="bg-bubbles">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <!-- end bubble effect -->
                            <div class="row justify-content-center align-items-center">
                                <div class="col-xl-7">
                                    <div class="p-0 p-sm-4 px-xl-0">
                                        <div id="reviewcarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-indicators carousel-indicators-rounded justify-content-start ms-0 mb-0">
                                                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                            </div>
                                            <!-- end carouselIndicators -->
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <div class="testi-contain text-white">
                                                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                                                        <h4 class="mt-4 fw-medium lh-base text-white">"الإدارة الفعّالة تعتمد على فهم قدرات كل فرد في الفريق وتوظيف تلك القدرات بأفضل طريقة ممكنة لتحقيق النتائج المرجوة. القائد الجيد يعرف متى يقدم الدعم ومتى يمنح الحرية."
                                                        </h4><br>

                                                    </div>
                                                </div>

                                                <div class="carousel-item">
                                                    <div class="testi-contain text-white">
                                                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                                                        <h4 class="mt-4 fw-medium lh-base text-white">"الإدارة ليست فقط اتخاذ القرارات وتوجيه الآخرين، بل هي القدرة على خلق بيئة عمل إيجابية تشجع على الإبداع والتعاون، حيث يكون الجميع جزءًا من الحلول وليس فقط المنفذين للمهام."</h4> <br>

                                                    </div>
                                                </div>

                                                <div class="carousel-item">
                                                    <div class="testi-contain text-white">
                                                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                                                        <h4 class="mt-4 fw-medium lh-base text-white">"المدير الناجح هو الذي يدرك أن النجاح لا يتحقق بالجهود الفردية، بل من خلال العمل الجماعي والتناغم بين الفريق. دوره هو تحفيز الفريق، تمكينه من النمو، وتوفير الأدوات اللازمة لتحقيق الأهداف المشتركة."</h4> <br>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end carousel-inner -->
                                        </div>
                                        <!-- end review carousel -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container fluid -->
        </div>


        <!-- JAVASCRIPT -->
        <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{ asset('libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{ asset('libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{ asset('libs/node-waves/waves.min.js')}}"></script>
        <script src="{{ asset('libs/feather-icons/feather.min.js')}}"></script>
        <script src="{{  asset('plugins/toastr/toastr.min.js')}}"></script>
        <script src="{{  asset('plugins/toastr/toastr.js')}}"></script>
        <!-- pace js -->
        <script src="{{ asset('libs/pace-js/pace.min.js')}}"></script>
        <!-- password addon init -->
        <script src="{{ asset('js/pages/pass-addon.init.js')}}"></script>

        @include('custom_js.custom_js')

        @php
            // Get the current route name
            $routeName = Route::currentRouteName();

            // Split \ route name to get the controller name
            $segments = explode('.', $routeName);

            // Get the controller name (assuming it's the first segment)
            $route = isset($segments[0]) ? $segments[0] : null;

        @endphp


        @if ($route == 'login_page')
        @include('custom_js.login_js')
        @endif



    </body>


<!-- Mirrored from themesbrand.com/minia/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 05 Aug 2024 05:37:11 GMT -->
</html>
