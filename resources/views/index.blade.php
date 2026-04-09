<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewBazzar — Shop the Future</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── CSS Variables ── */
        :root {
            --bg: #07080f;
            --surface: #0f1119;
            --surface2: #161924;
            --border: rgba(255, 255, 255, 0.07);
            --text: #eef0f8;
            --muted: #6b7280;
            --accent: #4f8ef7;
            --accent2: #a78bfa;
            --green: #34d399;
            --orange: #fb923c;
            --red: #f87171;
            --gold: #fbbf24;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* ── Noise Overlay ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* ── Typography ── */
        h1,
        h2,
        h3,
        .brand {
            font-family: 'Syne', sans-serif;
        }

        /* ────────────────────────────────────
           NAV
        ──────────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5vw;
            height: 68px;
            background: rgba(7, 8, 15, 0.85);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s;
        }

        .hero-card-main img {
    height: 250px;
    /* width: 268px; */
}

        .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .brand span {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 7px 16px;
            border-radius: 9px;
            transition: all 0.2s;
        }

        .nav-links a:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav {
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-ghost {
            color: var(--text);
            border: 1px solid var(--border);
            background: transparent;
        }

        .btn-ghost:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-primary-nav {
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
        }

        .btn-primary-nav:hover {
            opacity: 0.88;
            transform: translateY(-1px);
        }

        /* Cart icon */
        .cart-btn {
            position: relative;
            color: var(--muted);
            font-size: 1.2rem;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .cart-btn:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .cart-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 16px;
            height: 16px;
            background: var(--accent);
            border-radius: 50%;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile nav toggle */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.4rem;
            cursor: pointer;
        }

        /* ────────────────────────────────────
           HERO
        ──────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 120px 5vw 80px;
            gap: 60px;
            position: relative;
            overflow: hidden;
        }

        /* Gradient orbs */
        .hero::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(79, 142, 247, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero::before {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            background: rgba(79, 142, 247, 0.1);
            border: 1px solid rgba(79, 142, 247, 0.2);
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 24px;
            animation: fadeUp 0.6s ease both;
        }

        .hero-tag-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.4);
            }
        }

        .hero-title {
            font-size: clamp(2.8rem, 5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 22px;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 480px;
            margin-bottom: 36px;
            line-height: 1.7;
            font-weight: 300;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 30px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 8px 32px rgba(79, 142, 247, 0.28);
            font-family: 'Syne', sans-serif;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(79, 142, 247, 0.4);
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            background: var(--surface2);
            color: var(--text);
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border);
            transition: all 0.2s;
            font-family: 'Syne', sans-serif;
        }

        .btn-hero-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* Hero Stats */
        .hero-stats {
            display: flex;
            gap: 32px;
            margin-top: 50px;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.4s ease both;
        }

        .hero-stat-item {}

        .hero-stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .hero-stat-label {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            z-index: 1;
            animation: fadeRight 0.8s 0.2s ease both;
        }

        .hero-card-main {
            height: 250px;
            /* background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 28px;
            position: relative;
            overflow: hidden; */
        }

        .hero-card-main::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79, 142, 247, 0.04) 0%, rgba(167, 139, 250, 0.04) 100%);
            pointer-events: none;
        }

        .product-showcase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .product-mini {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            transition: all 0.25s;
            cursor: pointer;
        }

        .product-mini:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .product-mini-img {
            width: 100%;
            height: 100px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .product-mini-name {
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--text);
            margin-bottom: 4px;
        }

        .product-mini-price {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--accent);
        }

        .product-mini-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .floating-badge {
            position: absolute;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        .floating-badge-1 {
            top: -18px;
            right: 40px;
            animation-delay: 0s;
        }

        .floating-badge-2 {
            bottom: 20px;
            left: -20px;
            animation-delay: 1.5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeRight {
            from {
                opacity: 0;
                transform: translateX(24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ────────────────────────────────────
           CATEGORIES STRIP
        ──────────────────────────────────── */
        .categories-strip {
            padding: 25px 0;
            background: #313b47;
            border-bottom: 1px solid #eee;
            overflow: hidden;
        }

        .strip-label {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            color: #666;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .categories-slider-wrapper {
            overflow: hidden;
            position: relative;
        }

        .categories-slider {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            animation: scrollLeft 25s linear infinite;
            white-space: nowrap;
        }

        .categories-slider:hover {
            animation-play-state: paused;
            /* Pause on hover */
        }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .cat-pill:hover {
            background: #fff;
            border-color: #ff3c78;
            color: #ff3c78;
            transform: translateY(-2px);
        }

        .cat-pill.active {
            background: #ff3c78;
            color: white;
            border-color: #ff3c78;
        }

        /* Auto Scroll Animation */
        @keyframes scrollLeft {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .strip-label {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .categories-grid {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 6px;
            scrollbar-width: none;
        }

        .categories-grid::-webkit-scrollbar {
            display: none;
        }

        .cat-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 50px;
            background: var(--surface2);
            border: 1px solid var(--border);
            white-space: nowrap;
            text-decoration: none;
            color: var(--text);
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .cat-pill:hover,
        .cat-pill.active {
            border-color: var(--accent);
            background: rgba(79, 142, 247, 0.08);
            color: var(--accent);
        }

        .cat-pill i {
            font-size: 1rem;
        }

        /* ────────────────────────────────────
           SECTION WRAPPER
        ──────────────────────────────────── */
        .section-wrap {
            padding: 80px 5vw;
        }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .section-eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .section-h2 {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            line-height: 1.1;
        }

        .section-desc {
            color: var(--muted);
            font-size: 0.9rem;
            max-width: 380px;
            margin-top: 8px;
            font-weight: 300;
        }

        .btn-see-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-see-all:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* ────────────────────────────────────
           PRODUCT GRID
        ──────────────────────────────────── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(256px, 1fr));
            gap: 18px;
        }

        .product-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
            cursor: pointer;
        }

        .product-card:hover {
            border-color: rgba(79, 142, 247, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .product-img {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
        }

        .product-img-overlay {
            position: absolute;
            inset: 0;
            background: rgba(7, 8, 15, 0.5);
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .product-card:hover .product-img-overlay {
            opacity: 1;
        }

        .overlay-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .overlay-btn:hover {
            background: #3a7de8;
            transform: scale(1.1);
        }

        .overlay-btn.wish {
            background: var(--surface2);
            color: var(--text);
        }

        .overlay-btn.wish:hover {
            background: rgba(248, 113, 113, 0.2);
            color: var(--red);
        }

        .product-info {
            padding: 16px 18px 18px;
        }

        .product-cat {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .product-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            color: var(--gold);
            margin-bottom: 12px;
        }

        .product-rating span {
            color: var(--muted);
            font-size: 0.7rem;
        }

        .product-footer {
            display: flex;
            flex-direction: column;
            padding-top: 10px;
            /* align-items: center; */
            justify-content: space-between;
        }

        .product-price {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text);
        }

        .product-price-old {
            font-size: 0.8rem;
            color: var(--muted);
            text-decoration: line-through;
            margin-left: 6px;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-add-cart {
            padding: 8px 16px;
            margin-top: 12px;
            border-radius: 9px;
            background: rgba(79, 142, 247, 0.12);
            color: var(--accent);
            border: 1px solid rgba(79, 142, 247, 0.25);
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-add-cart:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .tag-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .tag-new {
            background: rgba(52, 211, 153, 0.2);
            color: var(--green);
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .tag-hot {
            background: rgba(251, 146, 60, 0.2);
            color: var(--orange);
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .tag-sale {
            background: rgba(248, 113, 113, 0.2);
            color: var(--red);
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        /* ────────────────────────────────────
           PROMO BANNER
        ──────────────────────────────────── */
        .promo-section {
            padding: 0 5vw 80px;
        }

        .promo-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
        }

        .promo-card {
            border-radius: 24px;
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .promo-card:hover {
            transform: scale(1.02);
        }

        .promo-card-1 {
            background: linear-gradient(135deg, rgba(79, 142, 247, 0.2) 0%, rgba(167, 139, 250, 0.15) 100%);
            border: 1px solid rgba(79, 142, 247, 0.2);
        }

        .promo-card-2 {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.15) 0%, rgba(79, 142, 247, 0.1) 100%);
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        .promo-emoji {
            position: absolute;
            top: 24px;
            right: 30px;
            font-size: 4.5rem;
            opacity: 0.6;
        }

        .promo-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(79, 142, 247, 0.2);
            color: var(--accent);
            margin-bottom: 10px;
            width: fit-content;
        }

        .promo-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .promo-sub {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 300;
            margin-bottom: 18px;
        }

        .promo-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            width: fit-content;
            transition: all 0.2s;
        }

        .promo-cta:hover {
            background: #3a7de8;
            transform: translateX(3px);
        }

        /* ────────────────────────────────────
           FEATURES
        ──────────────────────────────────── */
        .features-section {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 60px 5vw;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .feature-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
        }

        .feature-desc {
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 300;
            line-height: 1.6;
        }

        /* ────────────────────────────────────
           TESTIMONIALS
        ──────────────────────────────────── */
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }

        .review-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            transition: all 0.25s;
        }

        .review-card:hover {
            border-color: rgba(79, 142, 247, 0.3);
            transform: translateY(-3px);
        }

        .review-stars {
            display: flex;
            gap: 3px;
            color: var(--gold);
            font-size: 0.8rem;
            margin-bottom: 14px;
        }

        .review-text {
            font-size: 0.88rem;
            color: var(--text);
            line-height: 1.7;
            margin-bottom: 18px;
            font-weight: 300;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .review-av {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .review-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text);
        }

        .review-role {
            font-size: 0.72rem;
            color: var(--muted);
        }

        /* ────────────────────────────────────
           NEWSLETTER
        ──────────────────────────────────── */
        .newsletter-section {
            padding: 0 5vw 80px;
        }

        .newsletter-card {
            background: linear-gradient(135deg, rgba(79, 142, 247, 0.1) 0%, rgba(167, 139, 250, 0.08) 100%);
            border: 1px solid rgba(79, 142, 247, 0.18);
            border-radius: 28px;
            padding: 60px 5vw;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .newsletter-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 300px;
            background: radial-gradient(ellipse, rgba(79, 142, 247, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .newsletter-title {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 12px;
        }

        .newsletter-sub {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 32px;
            font-weight: 300;
        }

        .newsletter-form {
            display: flex;
            gap: 10px;
            max-width: 460px;
            margin: 0 auto;
            flex-wrap: wrap;
            justify-content: center;
        }

        .newsletter-input {
            flex: 1;
            min-width: 220px;
            padding: 13px 18px;
            border-radius: 12px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 0.88rem;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        .newsletter-input::placeholder {
            color: var(--muted);
        }

        .newsletter-input:focus {
            border-color: var(--accent);
        }

        .newsletter-btn {
            padding: 13px 26px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            font-size: 0.88rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Syne', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .newsletter-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* ────────────────────────────────────
           FOOTER
        ──────────────────────────────────── */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 60px 5vw 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-brand-desc {
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.7;
            margin-top: 14px;
            max-width: 260px;
            font-weight: 300;
        }

        .footer-socials {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        .social-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .social-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .footer-col-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 16px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 300;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .footer-links a i {
            font-size: 0.7rem;
        }

        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-copy {
            color: var(--muted);
            font-size: 0.78rem;
        }

        .footer-copy span {
            color: var(--accent);
        }

        .footer-payment {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-icon {
            padding: 4px 10px;
            border-radius: 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--muted);
        }

        /* ────────────────────────────────────
           RESPONSIVE
        ──────────────────────────────────── */
        @media (max-width: 900px) {
            .hero {
                grid-template-columns: 1fr;
                padding-top: 90px;
                text-align: center;
            }

            .hero-desc {
                margin: 0 auto 36px;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-visual {
                display: none;
            }

            .promo-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {

            .nav-links,
            .btn-ghost {
                display: none;
            }

            .nav-toggle {
                display: block;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* ── Scroll animation helper ── */
        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── FULL CART DRAWER STYLES (Dark Theme) ── */
        .cart-drawer {
            position: fixed;
            right: -420px;
            top: 0;
            width: 420px;
            height: 100vh;
            background: var(--surface);
            color: var(--text);
            box-shadow: -20px 0 40px rgba(0, 0, 0, 0.6);
            transition: right 0.4s cubic-bezier(0.32, 0.72, 0, 1);
            z-index: 1001;
            display: flex;
            flex-direction: column;
        }

        .cart-drawer.open {
            right: 0;
        }

        .cart-header {
            padding: 24px 28px 20px;
            border-bottom: 1px solid var(--border);
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            scrollbar-width: thin;
        }

        .cart-item {
            display: flex;
            gap: 16px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .cart-item-img {
            width: 72px;
            height: 72px;
            background: rgba(79, 142, 247, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            flex-shrink: 0;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .cart-item-price {
            color: var(--accent);
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .cart-qty {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .cart-qty button {
            width: 32px;
            height: 32px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cart-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 8, 15, 0.7);
            z-index: 1000;
            display: none;
        }

        button.logout-btn {
            padding: 10px;
            cursor: pointer;

        }
    </style>
</head>

<body>

    <!-- ── NAVBAR ── -->
    <nav class="navbar">
        <a href="{{ route('index') }}" class="brand">
            <div class="brand-icon">🛍️</div>
            New<span>Bazzar</span>
        </a>

        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Shop</a></li>
            <li><a href="#">Deals</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">About</a></li>
        </ul>

        <div class="nav-actions">
            <button class="cart-btn" onclick="openCart()">
                <i class="bi bi-bag"></i>
                <span class="cart-badge" id="cartNavBadge" style="display:none">0</span>
            </button>

            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="logout-btn" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-nav btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn-nav btn-primary-nav">Sign up</a>
            @endauth

            <button class="nav-toggle"
                onclick="this.parentElement.parentElement.querySelector('.nav-links').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-tag">
                <div class="hero-tag-dot"></div>
                🔥 New arrivals every day
            </div>

            <h1 class="hero-title">
                Shop Smarter,<br>
                <span class="gradient-text">Live Better</span>
            </h1>

            <p class="hero-desc">
                Discover thousands of curated products at unbeatable prices. From electronics to fashion — NewBazzar has
                everything you need, delivered fast.
            </p>

            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn-hero-primary">
                    <i class="bi bi-bag-fill"></i> Start Shopping
                </a>
                <a href="#" class="btn-hero-secondary">
                    <i class="bi bi-play-circle"></i> Browse Deals
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="hero-stat-num">50K+</div>
                    <div class="hero-stat-label">Products</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num">1.2M</div>
                    <div class="hero-stat-label">Happy Customers</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num">4.9★</div>
                    <div class="hero-stat-label">Average Rating</div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div style="position:relative;">
                <div class="floating-badge floating-badge-1">
                    <i class="bi bi-patch-check-fill" style="color:var(--green)"></i>
                    Order Delivered!
                </div>

                <div class="hero-card-main">
                    <img src="https://png.pngtree.com/background/20230618/original/pngtree-visualizing-e-commerce-in-benin-through-3d-rendering-for-social-media-picture-image_3753644.jpg"
                        alt="">
                </div>

                <div class="floating-badge floating-badge-2">
                    <i class="bi bi-truck" style="color:var(--accent)"></i>
                    Free shipping today!
                </div>
            </div>
        </div>
    </section>

    <!-- ── CATEGORIES AUTO SLIDER ── -->
    <section class="categories-strip">
        <div class="strip-label">Browse by category</div>

        <div class="categories-slider-wrapper">
            <div class="categories-slider" id="catSlider">
                <a href="#" class="cat-pill active" data-category="all">
                    <i class="bi bi-grid-fill"></i> All Products
                </a>
                <a href="#" class="cat-pill" data-category="electronics">
                    <i class="bi bi-phone-fill"></i> Electronics
                </a>
                <a href="#" class="cat-pill" data-category="fashion">
                    <i class="bi bi-bag-heart-fill"></i> Fashion
                </a>
                <a href="#" class="cat-pill" data-category="home">
                    <i class="bi bi-house-fill"></i> Home & Living
                </a>
                <a href="#" class="cat-pill" data-category="beauty">
                    <i class="bi bi-heart-pulse-fill"></i> Health & Beauty
                </a>
                <a href="#" class="cat-pill" data-category="gaming">
                    <i class="bi bi-controller"></i> Gaming
                </a>
                <a href="#" class="cat-pill" data-category="sports">
                    <i class="bi bi-bicycle"></i> Sports
                </a>
                <a href="#" class="cat-pill" data-category="books">
                    <i class="bi bi-book-fill"></i> Books
                </a>
                <a href="#" class="cat-pill" data-category="cameras">
                    <i class="bi bi-camera-fill"></i> Cameras
                </a>
                <a href="#" class="cat-pill" data-category="garden">
                    <i class="bi bi-flower1"></i> Garden
                </a>

                <!-- Duplicate items for smooth infinite scroll -->
                <a href="#" class="cat-pill active" data-category="all">
                    <i class="bi bi-grid-fill"></i> All Products
                </a>
                <a href="#" class="cat-pill" data-category="electronics">
                    <i class="bi bi-phone-fill"></i> Electronics
                </a>
                <a href="#" class="cat-pill" data-category="fashion">
                    <i class="bi bi-bag-heart-fill"></i> Fashion
                </a>
            </div>
        </div>
    </section>

    <!-- ── FEATURED PRODUCTS ── -->
    <section class="section-wrap">
        <div class="section-head reveal">
            <div>
                <div class="section-eyebrow">Handpicked for You</div>
                <h2 class="section-h2">Featured Products</h2>
                <p class="section-desc">Discover our most popular items loved by thousands of shoppers.</p>
            </div>
            <a href="#" class="btn-see-all">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="products-grid">
            @if (isset($products) && $products->isNotEmpty())

                @foreach ($products as $product)
                    <div class="product-card reveal">

                        <!-- Product Image -->
                        <div class="product-img"
                            style="background: linear-gradient(135deg, rgba(79,142,247,0.1), rgba(167,139,250,0.08)); position: relative; overflow: hidden;">

                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    style="width: 100%; height: 220px; object-fit: cover;">
                            @else
                                <div
                                    style="width: 100%; height: 220px; display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                                    {{ $product->emoji ?? '📦' }}
                                </div>
                            @endif

                            <!-- Hover Overlay -->
                            <div class="product-img-overlay">
                                <a href="#" class="overlay-btn"><i class="bi bi-bag-plus-fill"></i></a>
                                <a href="#" class="overlay-btn wish"><i class="bi bi-heart"></i></a>
                                <a href="{{ route('products.show', $product->id ?? '#') }}" class="overlay-btn"
                                    style="background:var(--surface2);color:var(--text)">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Badge -->
                        <span
                            class="tag-badge 
                    @if ($loop->first) tag-new 
                    @elseif($loop->iteration === 2) tag-hot 
                    @elseif($loop->iteration === 3) tag-sale 
                    @else tag-new @endif">

                            @if ($loop->first)
                                New
                            @elseif($loop->iteration === 2)
                                🔥 Hot
                            @elseif($loop->iteration === 3)
                                Sale
                            @else
                                New
                            @endif
                        </span>

                        <!-- Product Info -->
                        <div class="product-info">
                            <div class="product-cat">
                                {{ $product->category->name ?? 'General' }}
                            </div>

                            <div class="product-name">
                                {{ $product->name }}
                            </div>

                            <!-- Rating -->
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <span>(4.8 · 1.2k reviews)</span>
                            </div>

                            <!-- Footer -->
                            <div class="product-footer">
                                <div>
                                    <span class="product-price">
                                        ${{ number_format($product->price ?? 0, 2) }}
                                    </span>
                                    @if (!empty($product->old_price))
                                        <span class="product-price-old">
                                            ${{ number_format($product->old_price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <button class="btn-add-cart" 
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->price }}"
                                    data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-16 text-gray-500">
                    No products available yet.
                </div>
            @endif
        </div>
    </section>

    <!-- ── PROMO BANNERS ── -->
    <section class="promo-section">
        <div class="promo-grid reveal">
            <a href="#" class="promo-card promo-card-1">
                <div class="promo-emoji">🛍️</div>
                <div class="promo-tag">Limited Time</div>
                <div class="promo-title">Mega Sale — Up to<br>70% Off</div>
                <div class="promo-sub">On electronics, fashion, gadgets, and more. Don't miss out!</div>
                <div class="promo-cta">Shop Sale <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="#" class="promo-card promo-card-2">
                <div class="promo-emoji">📦</div>
                <div class="promo-tag">Members Only</div>
                <div class="promo-title">Free Shipping<br>All Orders</div>
                <div class="promo-sub">Join NewBazzar Premium today.</div>
                <div class="promo-cta">Join Now <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
    </section>

    <!-- ── FEATURES ── -->
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-item reveal">
                <div class="feature-icon" style="background:rgba(79,142,247,0.12);color:var(--accent)">
                    <i class="bi bi-truck-front-fill"></i>
                </div>
                <div class="feature-name">Fast Delivery</div>
                <div class="feature-desc">Get your orders delivered within 24–48 hours across 500+ cities with
                    real-time tracking.</div>
            </div>
            <div class="feature-item reveal">
                <div class="feature-icon" style="background:rgba(52,211,153,0.12);color:var(--green)">
                    <i class="bi bi-shield-check-fill"></i>
                </div>
                <div class="feature-name">Secure Payments</div>
                <div class="feature-desc">Shop with confidence using SSL-encrypted transactions and 100% fraud
                    protection.</div>
            </div>
            <div class="feature-item reveal">
                <div class="feature-icon" style="background:rgba(167,139,250,0.12);color:var(--accent2)">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="feature-name">Easy Returns</div>
                <div class="feature-desc">Not satisfied? Return within 30 days, no questions asked. Hassle-free
                    refunds.</div>
            </div>
            <div class="feature-item reveal">
                <div class="feature-icon" style="background:rgba(251,146,60,0.12);color:var(--orange)">
                    <i class="bi bi-headset"></i>
                </div>
                <div class="feature-name">24/7 Support</div>
                <div class="feature-desc">Our friendly support team is here around the clock via chat, email, or phone.
                </div>
            </div>
        </div>
    </section>

    <!-- ── TESTIMONIALS ── -->
    <section class="section-wrap">
        <div class="section-head reveal">
            <div>
                <div class="section-eyebrow">What People Say</div>
                <h2 class="section-h2">Customer Reviews</h2>
            </div>
        </div>

        <div class="reviews-grid">
            <div class="review-card reveal">
                <div class="review-stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <div class="review-text">Absolutely love NewBazzar! The delivery was lightning fast and the product
                    quality exceeded my expectations. Will definitely order again.</div>
                <div class="review-author">
                    <div class="review-av" style="background:linear-gradient(135deg,var(--accent),var(--accent2))">A
                    </div>
                    <div>
                        <div class="review-name">Alice Johnson</div>
                        <div class="review-role">Verified Buyer · Electronics</div>
                    </div>
                </div>
            </div>

            <div class="review-card reveal">
                <div class="review-stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                </div>
                <div class="review-text">Great prices and a huge selection. I found items here that I couldn't find
                    anywhere else. The app is clean and easy to navigate too!</div>
                <div class="review-author">
                    <div class="review-av" style="background:linear-gradient(135deg,var(--green),var(--accent))">R
                    </div>
                    <div>
                        <div class="review-name">Rahul Kumar</div>
                        <div class="review-role">Verified Buyer · Fashion</div>
                    </div>
                </div>
            </div>

            <div class="review-card reveal">
                <div class="review-stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <div class="review-text">Returned an item with zero issues. The support team was super helpful.
                    NewBazzar has definitely earned a loyal customer in me!</div>
                <div class="review-author">
                    <div class="review-av" style="background:linear-gradient(135deg,var(--orange),var(--red))">S</div>
                    <div>
                        <div class="review-name">Sara Williams</div>
                        <div class="review-role">Verified Buyer · Home & Living</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── NEWSLETTER ── -->
    <section class="newsletter-section">
        <div class="newsletter-card reveal">
            <div class="section-eyebrow" style="text-align:center;margin-bottom:10px">Stay in the Loop</div>
            <h2 class="newsletter-title">Get Exclusive Deals<br>& New Arrivals</h2>
            <p class="newsletter-sub">Join 200,000+ subscribers. No spam, ever. Unsubscribe anytime.</p>
            <div class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Enter your email address">
                <button class="newsletter-btn"><i class="bi bi-send-fill"></i> Subscribe</button>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <a href="/" class="brand" style="margin-bottom:4px;display:inline-flex">
                    <div class="brand-icon">🛍️</div>
                    New<span>Bazzar</span>
                </a>
                <p class="footer-brand-desc">
                    Your one-stop marketplace for everything. Quality products, fast delivery, and unbeatable prices —
                    that's the NewBazzar promise.
                </p>
                <div class="footer-socials">
                    <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div>
                <div class="footer-col-title">Shop</div>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-chevron-right"></i> New Arrivals</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Best Sellers</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Today's Deals</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> All Categories</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Gift Cards</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-col-title">Account</div>
                <ul class="footer-links">
                    <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right"></i> Sign In</a></li>
                    <li><a href="{{ route('register') }}"><i class="bi bi-chevron-right"></i> Register</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> My Orders</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Wishlist</a></li>
                    <li><a href="{{ route('profile') }}"><i class="bi bi-chevron-right"></i> Profile</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-col-title">Support</div>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Help Center</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Returns Policy</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Shipping Info</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Privacy Policy</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copy">
                © 2025 <span>NewBazzar</span>. All rights reserved.
            </div>
            <div class="footer-payment">
                <span class="payment-icon">VISA</span>
                <span class="payment-icon">MC</span>
                <span class="payment-icon">PayPal</span>
                <span class="payment-icon">UPI</span>
                <span class="payment-icon">Razorpay</span>
            </div>
        </div>
    </footer>

    @include('cart-drawer')

</body>

</html>