@php
    $seoSiteName = 'Sora Studio';
    $seoDefaultTitle = 'Sora Studio – Studio Ảnh Cưới Nghệ Thuật Hà Nội';
    $seoTitle = trim($__env->yieldContent('title')) ?: $seoDefaultTitle;
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'Sora Studio – studio ảnh cưới fine art tại Hà Nội. Phong cách Hàn Quốc tinh tế, concept sân thượng độc quyền, pre-wedding & chụp cưới trọn gói tại Sora Rooftop và Biệt Thự Sora.';
    $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'studio ảnh cưới Hà Nội, chụp ảnh cưới, pre-wedding, Sora Studio, Sora Bridal, ảnh cưới fine art, chụp cưới phong cách Hàn Quốc';
    $seoRobots = trim($__env->yieldContent('meta_robots')) ?: 'index, follow';
    $seoCanonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $seoOgImage = trim($__env->yieldContent('og_image')) ?: asset('user/anhTrangChu.webp');
    $seoOgType = trim($__env->yieldContent('og_type')) ?: 'website';
@endphp
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="author" content="{{ $seoSiteName }}">
<meta name="robots" content="{{ $seoRobots }}">
<meta name="theme-color" content="#1c1816">
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:locale" content="vi_VN">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:type" content="{{ $seoOgType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoOgImage }}">
<meta property="og:image:alt" content="{{ $seoSiteName }} – Studio ảnh cưới nghệ thuật Hà Nội">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoOgImage }}">
<meta name="twitter:image:alt" content="{{ $seoSiteName }} – Studio ảnh cưới nghệ thuật Hà Nội">

<link rel="icon" href="{{ asset('user/favicon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('user/favicon.svg') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ asset('user/anhTrangChu.webp') }}">

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('user/css/app-layout.css') }}">
<link rel="stylesheet" href="{{ asset('user/css/user-custom.css') }}">
@stack('styles')
@stack('meta')
</head>
