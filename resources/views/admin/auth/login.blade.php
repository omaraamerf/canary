<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دخول الإدارة | رفقنا</title>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/bootstrap-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/icons-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/app-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/canary-admin.css') }}">
</head>
<body>
<div class="auth-page">
    <div class="container-fluid p-0"><div class="row g-0">
        <div class="col-xxl-4 col-lg-5 col-md-6">
            <div class="auth-full-page-content d-flex p-sm-5 p-4"><div class="w-100"><div class="d-flex flex-column h-100">
                <div class="mb-4 mb-md-5 text-center">
                    <a href="{{ route('home') }}" class="d-block auth-logo"><img src="{{ asset('admin-assets/images/logo-sm.svg') }}" alt="" height="30"> <span class="logo-txt">رفقنا</span></a>
                </div>
                <div class="auth-content my-auto">
                    <div class="text-center"><h4 class="mb-0">مرحبًا بعودتك</h4><p class="text-muted mt-2">أدخل بيانات حساب الإدارة للمتابعة.</p></div>
                    <form class="mt-4 pt-2" method="post" action="{{ route('admin.login.store') }}">
                        @csrf
                        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                        <div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus></div>
                        <div class="mb-3"><label class="form-label">كلمة المرور</label><input class="form-control" type="password" name="password" required></div>
                        <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label" for="remember">تذكرني</label></div>
                        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">دخول</button>
                    </form>
                    <div class="mt-4 text-center"><a class="text-muted" href="{{ route('home') }}"><i class="bx bx-right-arrow-alt align-middle"></i> العودة إلى الموقع</a></div>
                </div>
                <div class="mt-4 mt-md-5 text-center"><p class="mb-0 text-muted">{{ date('Y') }} © رفقنا</p></div>
            </div></div></div>
        </div>
        <div class="col-xxl-8 col-lg-7 col-md-6">
            <div class="auth-bg pt-md-5 p-4 d-flex"><div class="bg-overlay bg-primary"></div><div class="auth-bg-copy position-relative text-white mt-auto p-4"><h2 class="text-white">إدارة السوق من مكان واحد</h2><p class="mb-0 text-white-50">تابع الطيور والوسائط وطلبات الحجز وحالات التسليم.</p></div></div>
        </div>
    </div></div>
</div>
</body>
</html>
