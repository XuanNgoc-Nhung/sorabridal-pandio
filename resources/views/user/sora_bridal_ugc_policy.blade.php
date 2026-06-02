
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chương Trình Chia Sẻ Kỷ Niệm — Sora Bridal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --cream:#FDF7F4;--cream-deep:#F5EBE5;
  --blush-light:#FAEAEA;--blush:#F0D4CC;--blush-mid:#E2B5AA;
  --rose:#C47C72;--rose-deep:#9E5248;--mauve:#6B3530;--dark:#2E1210;
  --text:#3A1C18;--muted:#9B6C67;--border:#EACCC5;--border-light:#F4DDD8;--white:#FFFFFF;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--text);font-size:15px;line-height:1.65;overflow-x:hidden}

/* ── WRAP ── */
.wrap{max-width:960px;margin:0 auto;padding:0 48px}

/* ── SECTION HEADER ── */
.sec-header{display:flex;align-items:baseline;gap:14px;margin-bottom:32px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.sec-n{font-family:'Playfair Display',serif;font-size:34px;font-style:italic;color:var(--blush);font-weight:400;line-height:1;flex-shrink:0}
.sec-label{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--rose);font-weight:500;margin-bottom:2px}
.sec-title{font-family:'Playfair Display',serif;font-size:26px;font-weight:400;line-height:1.15;color:var(--dark)}
.sec-title em{font-style:italic;color:var(--rose)}

/* ── HERO ── */
.hero{background:var(--dark);display:grid;grid-template-columns:3fr 2fr;position:relative;overflow:hidden;min-height:88vh}
.hero-bg{position:absolute;bottom:-20px;right:-10px;font-family:'Playfair Display',serif;font-size:220px;font-weight:700;font-style:italic;color:rgba(255,255,255,0.022);line-height:1;pointer-events:none;user-select:none}
.hero-left{padding:60px 44px 60px 60px;display:flex;flex-direction:column;justify-content:space-between;position:relative;z-index:2;border-right:1px solid rgba(255,255,255,0.06)}
.hero-right{padding:60px 44px;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:2}
.eyebrow-hero{display:flex;align-items:center;gap:12px;margin-bottom:36px}
.eyebrow-hero span{font-size:9px;letter-spacing:3.5px;text-transform:uppercase;color:var(--blush-mid);font-weight:400}
.eyebrow-hero::after{content:'';flex:1;height:1px;background:rgba(255,255,255,0.08)}
.hero-h1{font-family:'Playfair Display',serif;font-size:50px;font-weight:400;line-height:1.1;color:var(--cream);margin-bottom:18px}
.hero-h1 em{font-style:italic;color:var(--blush-mid);display:block}
.hero-div{width:32px;height:1px;background:var(--rose);margin:16px 0}
.hero-desc{font-size:13px;color:rgba(253,247,244,0.48);font-weight:300;line-height:1.85;margin-bottom:14px}
.hero-desc-em{font-size:13px;color:rgba(240,212,204,0.68);font-weight:300;line-height:1.8;font-style:italic;border-left:2px solid rgba(196,124,114,0.3);padding-left:14px}
.hero-stats{display:flex;flex-direction:column;gap:22px}
.hero-stat{border-left:1px solid rgba(240,212,204,0.18);padding-left:18px}
.stat-num{font-family:'Playfair Display',serif;font-size:42px;font-weight:400;font-style:italic;color:var(--blush);line-height:1;margin-bottom:2px}
.stat-lbl{font-size:11px;color:rgba(253,247,244,0.32);font-weight:300;line-height:1.45}

/* ── 01 QUYỀN LỢI ── */
.sec-benefits{padding:56px 0 44px}
.benefits-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;background:var(--border);border-radius:4px;overflow:hidden}
.b-item{display:flex;gap:16px;padding:22px 22px;background:var(--white)}
.b-n{font-family:'Playfair Display',serif;font-size:20px;font-style:italic;color:var(--blush);font-weight:400;line-height:1;flex-shrink:0;min-width:26px;padding-top:1px}
.b-item h4{font-size:13.5px;font-weight:500;color:var(--dark);margin-bottom:4px}
.b-item p{font-size:12.5px;color:var(--muted);font-weight:300;line-height:1.65}

/* ── VIDEO RECAP BAND ── */
.recap-band{background:var(--blush);padding:44px 0}
.recap-inner{max-width:960px;margin:0 auto;padding:0 48px;display:grid;grid-template-columns:80px 1fr;gap:28px;align-items:center}
.recap-icon{font-size:44px;text-align:center}
.recap-tag{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--rose-deep);font-weight:500;margin-bottom:6px}
.recap-content h3{font-family:'Playfair Display',serif;font-size:20px;font-weight:500;color:var(--dark);margin-bottom:6px;line-height:1.3}
.recap-content p{font-size:13px;color:var(--mauve);font-weight:300;line-height:1.75}

/* ── 02 NỘI DUNG ── */
.sec-content{padding:44px 0}
.content-intro{font-size:13px;color:var(--muted);font-weight:300;line-height:1.8;margin-bottom:10px}
.content-tip{background:var(--blush-light);border-left:3px solid var(--rose);padding:10px 16px;border-radius:0 3px 3px 0;margin-bottom:24px;font-size:12.5px;color:var(--mauve);font-weight:300;line-height:1.6}
.content-tip strong{font-weight:500;color:var(--rose-deep)}

/* Mobile-first content cards */
.ct-cards{display:flex;flex-direction:column;gap:2px}
.ct-card{background:var(--white);border:1px solid var(--border-light);border-radius:4px;overflow:hidden}
.ct-card-head{display:grid;grid-template-columns:1fr auto;gap:16px;align-items:start;padding:20px 20px 0}
.ct-card-meta h3{font-family:'Playfair Display',serif;font-size:18px;font-weight:500;color:var(--dark);margin-bottom:2px;line-height:1.2}
.ct-card-meta .dur{font-size:11px;color:var(--muted);font-weight:300;margin-bottom:10px;line-height:1.4}
.ct-icon-wrap{font-size:22px}
.gift-pill{display:inline-flex;flex-direction:column;background:var(--blush-light);border-radius:3px;padding:7px 11px;margin-bottom:16px}
.gift-pill .gl{font-size:8.5px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);font-weight:400;margin-bottom:1px}
.gift-pill .gv{font-size:13px;font-weight:500;color:var(--rose-deep);line-height:1.3}
.gift-pill .gs{font-size:11px;color:var(--muted);font-weight:300}
.ct-card-body{display:grid;grid-template-columns:1fr 1fr;gap:0;border-top:1px solid var(--border-light)}
.ct-col{padding:14px 20px}
.ct-col+.ct-col{border-left:1px solid var(--border-light)}
.ct-col-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--rose);font-weight:500;margin-bottom:8px}
.ct-item{font-size:12.5px;color:var(--text);font-weight:300;padding:5px 0 5px 13px;position:relative;border-bottom:1px solid var(--border-light);line-height:1.5}
.ct-item:last-child{border-bottom:none}
.ct-item::before{content:'';position:absolute;left:0;top:12px;width:5px;height:1px;background:var(--blush-mid)}
.ct-item.italic{color:var(--rose-deep);font-style:italic}
.ct-item.italic::before{background:var(--rose)}

/* ── 03 COMBO ── */
.sec-combo{padding:44px 0;background:var(--dark);margin:0}
.sec-combo .sec-n{color:rgba(226,181,170,0.3)}
.sec-combo .sec-label{color:var(--blush-mid)}
.sec-combo .sec-title{color:var(--cream)}
.sec-combo .sec-title em{color:var(--blush-mid)}
.sec-combo-desc{font-size:13px;color:rgba(253,247,244,0.42);font-weight:300;line-height:1.8;margin-top:6px;margin-bottom:28px}
/* value bar */
.cvbar{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:4px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:16px;overflow-x:auto}
.cvbar-lbl{font-size:8.5px;letter-spacing:2.5px;text-transform:uppercase;color:rgba(253,247,244,0.25);font-weight:400;white-space:nowrap;flex-shrink:0}
.cvbar-items{display:flex;gap:0;flex:1;min-width:0}
.cvi{flex:1;text-align:center;padding:0 10px;border-right:1px solid rgba(255,255,255,0.05)}
.cvi:last-child{border-right:none}
.cvi .cvn{font-family:'Playfair Display',serif;font-size:10px;font-style:italic;color:var(--blush-mid);font-weight:400;margin-bottom:2px}
.cvi .cvv{font-size:11px;color:rgba(253,247,244,0.45);font-weight:300;line-height:1.35}
.cvi.hl .cvn{color:var(--rose)}
.cvi.hl .cvv{color:var(--blush-mid);font-weight:500}
/* combo cards */
.combo-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden}
.cc{background:#3A1A14;padding:24px 24px;position:relative}
.cc.c3{background:#1A0A08;grid-column:1/-1;display:grid;grid-template-columns:1fr 1fr;gap:40px;border:1px solid rgba(196,124,114,0.2)}
.cc.c4{background:#261008;grid-column:1/-1;display:grid;grid-template-columns:1fr 1fr;gap:40px}
.cc-badge{position:absolute;top:18px;right:18px}
.cc-badge span{background:var(--rose);color:#fff;font-size:8px;letter-spacing:2px;text-transform:uppercase;font-weight:500;padding:4px 9px;border-radius:2px}
.cc-tag{font-size:8.5px;letter-spacing:2.5px;text-transform:uppercase;color:rgba(226,181,170,0.45);font-weight:500;margin-bottom:7px}
.cc.c3 .cc-tag{color:var(--rose)}
.cc-name{font-family:'Playfair Display',serif;font-size:20px;font-weight:500;color:var(--cream);margin-bottom:3px;line-height:1.2}
.cc.c3 .cc-name{font-size:26px;color:#fff}
.cc-sub{font-size:11px;color:rgba(253,247,244,0.32);font-weight:300;margin-bottom:16px;line-height:1.4}
.cc.c3 .cc-sub{color:rgba(253,247,244,0.48)}
.cc-lines{list-style:none;margin-bottom:18px}
.cc-lines li{font-size:12px;color:rgba(253,247,244,0.55);font-weight:300;padding:5px 0 5px 14px;position:relative;border-bottom:1px solid rgba(255,255,255,0.04);line-height:1.4}
.cc.c3 .cc-lines li{color:rgba(253,247,244,0.72)}
.cc-lines li:last-child{border-bottom:none}
.cc-lines li::before{content:'';position:absolute;left:0;top:13px;width:6px;height:1px;background:var(--rose);opacity:.45}
.cc.c3 .cc-lines li::before{opacity:1}
.cc-gift-lbl{font-size:8.5px;letter-spacing:2px;text-transform:uppercase;color:rgba(253,247,244,0.22);margin-bottom:8px}
.cc.c3 .cc-gift-lbl{color:rgba(253,247,244,0.38)}
.cc-gifts{display:flex;flex-direction:column;gap:10px}
.cc-gift-item{display:flex;align-items:flex-start;gap:9px}
.cc-gift-dot{width:4px;height:4px;border-radius:50%;background:var(--rose);flex-shrink:0;margin-top:7px;opacity:.6}
.cc.c3 .cc-gift-dot{opacity:1;width:5px;height:5px;margin-top:6px}
.cc-gift-name{font-family:'Playfair Display',serif;font-size:14px;font-weight:400;font-style:italic;color:var(--blush-mid);line-height:1.25}
.cc.c3 .cc-gift-name{font-size:17px;color:#F0D4CC}
.cc-gift-sub{font-size:11px;color:rgba(253,247,244,0.28);font-weight:300;margin-top:1px}
.cc.c3 .cc-gift-sub{color:rgba(253,247,244,0.42)}

/* ── 04 QUY TRÌNH ── */
.sec-process{padding:44px 0;background:var(--cream-deep)}
.process-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:2px;background:var(--border);border-radius:4px;overflow:hidden;margin-top:28px}
.p-step{background:var(--white);padding:20px 18px;text-align:center}
.p-dot{width:40px;height:40px;border-radius:50%;background:var(--blush-light);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto 12px}
.p-num{font-family:'Playfair Display',serif;font-size:17px;font-weight:400;font-style:italic;color:var(--rose)}
.p-step h4{font-size:13px;font-weight:500;color:var(--dark);margin-bottom:5px}
.p-step p{font-size:12px;color:var(--muted);font-weight:300;line-height:1.6}
.p-note{background:var(--white);border:1px solid var(--border-light);border-radius:3px;padding:12px 18px;margin-top:14px;font-size:12.5px;color:var(--mauve);font-weight:300;line-height:1.65;text-align:center}
.p-note strong{color:var(--rose-deep);font-weight:500}

/* ── 05 CHÍNH SÁCH ── */
.sec-policy{padding:44px 0}
.policy-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:8px}
.policy-item{display:flex;gap:13px;padding:16px 16px;background:var(--white);border:1px solid var(--border-light);border-radius:3px}
.policy-icon{font-size:15px;flex-shrink:0;margin-top:1px}
.policy-item h4{font-size:13px;font-weight:500;color:var(--dark);margin-bottom:3px}
.policy-item p{font-size:12px;color:var(--muted);font-weight:300;line-height:1.65}
.policy-ast{background:var(--cream-deep);border-left:3px solid var(--blush-mid);border-radius:0 3px 3px 0;padding:11px 16px;margin-top:14px;font-size:12px;color:var(--muted);font-weight:300;line-height:1.7;font-style:italic;grid-column:1/-1}

/* ── 06 PHÁP LÝ ── */
.sec-legal{background:var(--blush);padding:44px 0}
.legal-inner{max-width:960px;margin:0 auto;padding:0 48px}
.legal-cols{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px}
.lc{background:rgba(255,255,255,0.6);border-radius:4px;padding:22px 20px;border:1px solid var(--border)}
.lc h4{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--rose-deep);font-weight:500;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border)}
.lc ul{list-style:none}
.lc ul li{font-size:12.5px;color:var(--mauve);font-weight:300;line-height:1.65;padding:7px 0 7px 15px;position:relative;border-bottom:1px solid rgba(196,124,114,0.1)}
.lc ul li:last-child{border-bottom:none}
.lc ul li::before{content:'—';position:absolute;left:0;color:var(--blush-mid);font-size:10px;top:8px}

/* ── FOOTER ── */
.footer{background:var(--dark);padding:40px 60px;display:flex;justify-content:space-between;align-items:center;gap:20px}
.footer-logo{font-family:'Playfair Display',serif;font-size:26px;font-weight:400;color:var(--cream);letter-spacing:3px}
.footer-logo em{font-style:italic;color:var(--blush-mid)}
.footer-thanks{font-family:'Playfair Display',serif;font-size:14px;font-weight:400;font-style:italic;color:rgba(240,212,204,0.42);text-align:right;line-height:1.6;max-width:340px}

/* ════════════════════════════════════════
   MOBILE  ≤ 600px
════════════════════════════════════════ */
@media(max-width:600px){
  body{font-size:14px}
  .wrap{padding:0 18px}

  /* hero */
  .hero{grid-template-columns:1fr;min-height:auto}
  .hero-right{display:none}
  .hero-left{padding:40px 20px}
  .hero-h1{font-size:36px}
  .hero-desc,.hero-desc-em{font-size:13px}

  /* recap */
  .recap-inner{padding:0 18px;grid-template-columns:48px 1fr;gap:16px}
  .recap-icon{font-size:32px}
  .recap-content h3{font-size:16px}

  /* sec header */
  .sec-n{font-size:26px}
  .sec-title{font-size:21px}

  /* benefits */
  .sec-benefits{padding:36px 0 28px}
  .benefits-grid{grid-template-columns:1fr}

  /* content cards */
  .sec-content{padding:32px 0}
  .ct-card-head{grid-template-columns:1fr auto}
  .ct-card-meta h3{font-size:16px}
  .ct-card-body{grid-template-columns:1fr}
  .ct-col+.ct-col{border-left:none;border-top:1px solid var(--border-light)}

  /* combo */
  .sec-combo{padding:36px 0}
  .combo-grid{grid-template-columns:1fr}
  .cc.c3,.cc.c4{grid-template-columns:1fr;gap:20px}
  .cc-badge{top:14px;right:14px}
  .cc-name{font-size:18px}
  .cc.c3 .cc-name{font-size:22px}
  .cvbar{flex-direction:column;gap:10px;align-items:flex-start}
  .cvbar-items{flex-wrap:wrap;gap:4px}
  .cvi{flex:none;border-right:none;border-bottom:1px solid rgba(255,255,255,0.05);padding:6px 0;text-align:left;width:100%}
  .cvi:last-child{border-bottom:none}

  /* process */
  .sec-process{padding:32px 0}
  .process-steps{grid-template-columns:1fr 1fr}

  /* policy */
  .sec-policy{padding:32px 0}
  .policy-grid{grid-template-columns:1fr}
  .policy-ast{grid-column:1}

  /* legal */
  .sec-legal{padding:32px 0}
  .legal-inner{padding:0 18px}
  .legal-cols{grid-template-columns:1fr}

  /* footer */
  .footer{flex-direction:column;text-align:center;padding:36px 20px}
  .footer-thanks{text-align:center;max-width:100%}
}

/* tablet 601–800px */
@media(min-width:601px) and (max-width:800px){
  .wrap{padding:0 28px}
  .hero-h1{font-size:42px}
  .hero-right{display:none}
  .hero-left{padding:48px 28px}
  .combo-grid{grid-template-columns:1fr}
  .cc.c3,.cc.c4{grid-template-columns:1fr;gap:24px}
  .legal-inner{padding:0 28px}
}
</style>
</head>
<body>

<!-- ══ HERO ══ -->
<section class="hero">
  <div class="hero-bg">Sora</div>
  <div class="hero-left">
    <div>
      <div class="eyebrow-hero"><span>Sora Bridal · Chương trình nội dung cộng đồng</span></div>
      <h1 class="hero-h1">Kỷ niệm của bạn<br>xứng đáng được<br><em>chia sẻ & lan tỏa</em></h1>
      <div class="hero-div"></div>
      <p class="hero-desc">Sora Bridal tin rằng mỗi cặp Dâu Rể đều mang theo một câu chuyện tình yêu độc nhất. Những bức ảnh cưới lộng lẫy có thể do Sora tạo ra — nhưng cảm xúc chân thực, tiếng cười rộn rã, hay sự hồi hộp chờ ngày trọng đại... thì chỉ bạn mới kể lại được trọn vẹn nhất.</p>
      <p class="hero-desc-em">Thay vì những lời quảng cáo hào nhoáng, chúng tôi muốn chính các Dâu Rể trở thành "Đại sứ Cảm xúc". Hãy để Sora Bridal tri ân những thước phim thanh xuân tuyệt vời của bạn.</p>
    </div>
  </div>
  <div class="hero-right">
    <div class="hero-stats">
      <div class="hero-stat"><div class="stat-num">4</div><div class="stat-lbl">loại nội dung<br>có thể thực hiện</div></div>
      <div class="hero-stat"><div class="stat-num">4</div><div class="stat-lbl">gói combo<br>để lựa chọn</div></div>
      <div class="hero-stat"><div class="stat-num">∞</div><div class="stat-lbl">kỷ niệm được<br>lưu giữ mãi mãi</div></div>
    </div>
  </div>
</section>

<!-- ══ 01. QUYỀN LỢI ══ -->
<section class="sec-benefits">
  <div class="wrap">
    <div class="sec-header">
      <div class="sec-n">01</div>
      <div>
        <div class="sec-label">Quyền lợi</div>
        <div class="sec-title">Bạn nhận được gì<span style="color:var(--rose)">?</span></div>
      </div>
    </div>
    <div class="benefits-grid">
      <div class="b-item">
        <div class="b-n">01</div>
        <div>
          <h4>Quà tri ân được định giá rõ ràng</h4>
          <p>Mỗi nội dung và combo đều có quà cụ thể — in ấn kỷ niệm, sweet box đến hoàn tiền và voucher. Minh bạch, không mơ hồ.</p>
        </div>
      </div>
      <div class="b-item">
        <div class="b-n">02</div>
        <div>
          <h4>Tự do sáng tạo, có ekip hỗ trợ</h4>
          <p>Không yêu cầu follower. Không kịch bản cứng nhắc. Team media Sora đồng hành từ ý tưởng đến thành phẩm.</p>
        </div>
      </div>
      <div class="b-item">
        <div class="b-n">03</div>
        <div>
          <h4>Được ghi nhận trên kênh chính thức</h4>
          <p>Nội dung của bạn có thể được Sora Bridal repost và chia sẻ — kèm credit đầy đủ và sự trân trọng thực sự.</p>
        </div>
      </div>
      <div class="b-item">
        <div class="b-n">04</div>
        <div>
          <h4>Quy trình minh bạch, đúng hẹn</h4>
          <p>Sau khi nội dung được duyệt, quà tặng gửi đến bạn trong đúng thời hạn cam kết — không điều khoản ẩn.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ VIDEO RECAP BAND ══ -->
<div class="recap-band">
  <div class="recap-inner">
    <div class="recap-icon">🎬</div>
    <div class="recap-content">
      <div class="recap-tag">Quà tặng khi đăng ký — không điều kiện</div>
      <h3>Video Recap buổi chụp ảnh cưới — Sora tặng ngay khi bạn tham gia</h3>
      <p>Dù bạn chưa đăng bất kỳ nội dung nào, đội ngũ Sora Bridal sẽ trao tặng 1 video recap chuyên nghiệp ghi lại trọn vẹn hành trình buổi chụp — một món quà kỷ niệm để lưu giữ mãi mãi.</p>
    </div>
  </div>
</div>

<!-- ══ 02. NỘI DUNG & QUÀ ══ -->
<section class="sec-content">
  <div class="wrap">
    <div class="sec-header">
      <div class="sec-n">02</div>
      <div>
        <div class="sec-label">Nội dung & quà tri ân</div>
        <div class="sec-title">Chọn loại nội dung <em>phù hợp với bạn</em></div>
      </div>
    </div>
    <p class="content-intro">Mỗi dâu rể có thế mạnh khác nhau. <strong style="color:var(--dark)">Chỉ cần chọn 1 trong 4 loại</strong> là đủ điều kiện nhận quà — hoặc kết hợp nhiều loại để nhận Gói Combo giá trị hơn nhiều.</p>
    <div class="content-tip"><strong>Gợi ý:</strong> Làm càng nhiều loại nội dung → quà càng có giá trị. Xem <strong>03. Gói Combo</strong> bên dưới.</div>

    <div class="ct-cards">

      <!-- Ảnh -->
      <div class="ct-card">
        <div class="ct-card-head">
          <div class="ct-card-meta">
            <h3>📸 Bài đăng ảnh</h3>
            <div class="dur">Feed / Story / Carousel</div>
            <div class="gift-pill">
              <span class="gl">Sora gửi tặng</span>
              <span class="gv">100.000đ / bài</span>
            </div>
          </div>
        </div>
        <div class="ct-card-body">
          <div class="ct-col">
            <div class="ct-col-label">Chủ đề gợi ý</div>
            <div class="ct-item">Ảnh cưới hoàn chỉnh sau chỉnh sửa</div>
            <div class="ct-item">Ảnh mẫu váy cưới / áo dài tại cửa hàng</div>
            <div class="ct-item">Ảnh váy cưới trong ngày cưới thực tế</div>
            <div class="ct-item">Ảnh hậu trường ekip, concept, trang phục</div>
            <div class="ct-item">Ảnh make up & styling</div>
          </div>
          <div class="ct-col">
            <div class="ct-col-label">Yêu cầu đăng tải</div>
            <div class="ct-item">Đăng TikTok và/hoặc Facebook</div>
            <div class="ct-item">Kèm hashtag quy định của Sora</div>
            <div class="ct-item">Tag tài khoản chính thức Sora Bridal</div>
            <div class="ct-item">Tài khoản để chế độ <strong>Public</strong></div>
          </div>
        </div>
      </div>

      <!-- Video ngắn -->
      <div class="ct-card">
        <div class="ct-card-head">
          <div class="ct-card-meta">
            <h3>✨ Video ngắn TikTok / Reels</h3>
            <div class="dur">Dưới 30 giây + caption &nbsp;·&nbsp; <em style="color:var(--rose)">Không cần đọc thoại</em></div>
            <div class="gift-pill">
              <span class="gl">Sora gửi tặng</span>
              <span class="gv">Ảnh bàn 15×21</span>
            </div>
          </div>
        </div>
        <div class="ct-card-body">
          <div class="ct-col">
            <div class="ct-col-label">Chủ đề gợi ý</div>
            <div class="ct-item">Váy ngày chụp / váy ngày cưới kèm caption</div>
            <div class="ct-item">Hậu trường ngày chụp kèm caption</div>
            <div class="ct-item">Sản phẩm dịch vụ nhận được kèm caption</div>
            <div class="ct-item">Trải nghiệm make up kèm caption</div>
            <div class="ct-item italic">✦ Hoặc tự do sáng tạo theo cách của bạn</div>
          </div>
          <div class="ct-col">
            <div class="ct-col-label">Yêu cầu đăng tải</div>
            <div class="ct-item">Đăng <strong>TikTok</strong> (bắt buộc)</div>
            <div class="ct-item">Facebook tùy chọn</div>
            <div class="ct-item">Kèm hashtag quy định của Sora</div>
            <div class="ct-item">Tag tài khoản chính thức Sora Bridal</div>
            <div class="ct-item">Tài khoản để chế độ <strong>Public</strong></div>
          </div>
        </div>
      </div>

      <!-- Video dài -->
      <div class="ct-card">
        <div class="ct-card-head">
          <div class="ct-card-meta">
            <h3>🎬 Video review dài</h3>
            <div class="dur">30 giây – 1 phút 30 giây</div>
            <div class="gift-pill">
              <span class="gl">Sora gửi tặng</span>
              <span class="gv">Sweet Box nến thơm</span>
              <span class="gs">Trị giá 250.000đ</span>
            </div>
          </div>
        </div>
        <div class="ct-card-body">
          <div class="ct-col">
            <div class="ct-col-label">Chủ đề gợi ý</div>
            <div class="ct-item">Review sản phẩm cưới đã nhận</div>
            <div class="ct-item">Review ảnh cưới sau chỉnh sửa hoàn thiện</div>
            <div class="ct-item">Review váy cưới / áo dài tại Sora</div>
            <div class="ct-item">Review trải nghiệm buổi chụp ảnh cưới</div>
            <div class="ct-item">Review đội ngũ ekip chăm sóc xuyên suốt</div>
            <div class="ct-item">Lý do chọn Sora Bridal cho ngày trọng đại</div>
          </div>
          <div class="ct-col">
            <div class="ct-col-label">Yêu cầu đăng tải</div>
            <div class="ct-item">Đăng <strong>TikTok</strong> (bắt buộc)</div>
            <div class="ct-item">Facebook tùy chọn</div>
            <div class="ct-item">Kèm hashtag quy định của Sora</div>
            <div class="ct-item">Tag tài khoản chính thức Sora Bridal</div>
            <div class="ct-item">Tài khoản để chế độ <strong>Public</strong></div>
          </div>
        </div>
      </div>

      <!-- Hội nhóm -->
      <div class="ct-card">
        <div class="ct-card-head">
          <div class="ct-card-meta">
            <h3>✍️ Review hội nhóm</h3>
            <div class="dur">Bài viết chia sẻ cảm nhận &nbsp;·&nbsp; Nhóm ≥ 5.000 thành viên</div>
            <div class="gift-pill">
              <span class="gl">Sora gửi tặng</span>
              <span class="gv">Sweet Box nến thơm</span>
              <span class="gs">Trị giá 250.000đ</span>
            </div>
          </div>
        </div>
        <div class="ct-card-body">
          <div class="ct-col">
            <div class="ct-col-label">Chủ đề gợi ý</div>
            <div class="ct-item">Cảm nhận về trang phục cưới hỏi của Sora</div>
            <div class="ct-item">Cảm nhận về ekip chụp ảnh</div>
            <div class="ct-item">Cảm nhận về sản phẩm / dịch vụ nhận được</div>
            <div class="ct-item">Cảm nhận về trải nghiệm make up</div>
            <div class="ct-item">Cảm nhận chung về toàn bộ hành trình</div>
            <div class="ct-item italic">✦ Hoặc tự do chia sẻ theo cách của bạn</div>
          </div>
          <div class="ct-col">
            <div class="ct-col-label">Yêu cầu đăng tải</div>
            <div class="ct-item">Đăng trong hội nhóm cưới hỏi / làm đẹp</div>
            <div class="ct-item">Nhóm tối thiểu 5.000 thành viên</div>
            <div class="ct-item">Tag tài khoản chính thức Sora Bridal</div>
            <div class="ct-item">Tài khoản để chế độ <strong>Public</strong></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══ 03. COMBO ══ -->
<section class="sec-combo">
  <div class="wrap">
    <div class="sec-header" style="border-color:rgba(255,255,255,0.07)">
      <div class="sec-n">03</div>
      <div>
        <div class="sec-label">Gói combo</div>
        <div class="sec-title">Kết hợp nội dung — <em>quà nhân lên gấp bội</em></div>
      </div>
    </div>
    <p class="sec-combo-desc">Mỗi bước lên Combo là một bước nhận được phần quà vượt xa tổng giá trị từng bài lẻ. Đây là cách Sora Bridal tri ân những người dám chia sẻ trọn vẹn nhất.</p>

    <div class="cvbar">
      <span class="cvbar-lbl">Giá trị quà</span>
      <div class="cvbar-items">
        <div class="cvi"><div class="cvn">Combo 01</div><div class="cvv">Bộ ảnh bàn đôi</div></div>
        <div class="cvi"><div class="cvn">Combo 02</div><div class="cvv">Ảnh + Sweet box</div></div>
        <div class="cvi hl"><div class="cvn">✦ Combo 03</div><div class="cvv">Ảnh phóng + Voucher 500k</div></div>
        <div class="cvi"><div class="cvn">Combo 04</div><div class="cvv">Quà premium + 1tr + Voucher</div></div>
      </div>
    </div>

    <div class="combo-grid">
      <!-- 1 -->
      <div class="cc">
        <div class="cc-tag">Combo 01 · 2 loại nội dung</div>
        <div class="cc-name">Khởi đầu hạnh phúc</div>
        <div class="cc-sub">Nhẹ nhàng, ý nghĩa</div>
        <ul class="cc-lines">
          <li>01 bài đăng hình ảnh</li>
          <li>01 video ngắn TikTok / Reels</li>
        </ul>
        <div class="cc-gift-lbl">Sora gửi tặng</div>
        <div class="cc-gifts">
          <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Ảnh bàn 15×21</div><div class="cc-gift-sub">In kỷ niệm từ bộ ảnh cưới của bạn</div></div></div>
          <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Ảnh bàn 20×30</div><div class="cc-gift-sub">Bộ đôi — phòng khách & phòng ngủ</div></div></div>
        </div>
      </div>
      <!-- 2 -->
      <div class="cc">
        <div class="cc-tag">Combo 02 · 2 loại nội dung</div>
        <div class="cc-name">Thước phim cảm xúc</div>
        <div class="cc-sub">Nội dung chất lượng, quà xứng đáng hơn</div>
        <ul class="cc-lines">
          <li>01 bài đăng hình ảnh</li>
          <li>01 video review dài (30s – 1p30s)</li>
        </ul>
        <div class="cc-gift-lbl">Sora gửi tặng</div>
        <div class="cc-gifts">
          <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Ảnh bàn 20×30</div><div class="cc-gift-sub">In kỷ niệm từ bộ ảnh cưới của bạn</div></div></div>
          <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Sweet Box nến thơm</div><div class="cc-gift-sub">Trị giá 250.000đ</div></div></div>
        </div>
      </div>
      <!-- 3 HERO -->
      <div class="cc c3">
        <div class="cc-badge"><span>✦ Được chọn nhiều nhất</span></div>
        <div>
          <div class="cc-tag">Combo 03 · 3 loại nội dung</div>
          <div class="cc-name">Đại sứ tình yêu</div>
          <div class="cc-sub">Bộ quà ý nghĩa nhất — kỷ niệm được trân trọng trọn vẹn</div>
          <ul class="cc-lines">
            <li>01 bài đăng hình ảnh</li>
            <li>01 video review dài (30s – 1p30s)</li>
            <li>01 bài review trên hội nhóm</li>
          </ul>
        </div>
        <div>
          <div class="cc-gift-lbl">Sora gửi tặng</div>
          <div class="cc-gifts">
            <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Ảnh phóng 60×90 Laminate cao cấp</div><div class="cc-gift-sub">Trị giá 850.000đ · Treo tường phòng khách — kỷ niệm hiện diện mãi trong ngôi nhà của bạn</div></div></div>
            <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Voucher update dòng váy cưới</div><div class="cc-gift-sub">Trị giá 500.000đ · Áp dụng cho lần thuê tiếp theo</div></div></div>
          </div>
        </div>
      </div>
      <!-- 4 -->
      <div class="cc c4">
        <div>
          <div class="cc-tag">Combo 04 · 4 loại nội dung</div>
          <div class="cc-name">Trọn vẹn thanh xuân</div>
          <div class="cc-sub">Trọn bộ — phần thưởng cao nhất</div>
          <ul class="cc-lines">
            <li>01 bài đăng hình ảnh</li>
            <li>01 video review dài (30s – 1p30s)</li>
            <li>01 video ngắn TikTok / Reels</li>
            <li>01 bài review trên hội nhóm</li>
          </ul>
        </div>
        <div>
          <div class="cc-gift-lbl">Sora gửi tặng</div>
          <div class="cc-gifts">
            <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Quà tặng premium</div><div class="cc-gift-sub">Trị giá 500.000đ</div></div></div>
            <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Hoàn tiền 1.000.000đ</div><div class="cc-gift-sub">Chuyển khoản trực tiếp sau khi duyệt</div></div></div>
            <div class="cc-gift-item"><div class="cc-gift-dot"></div><div><div class="cc-gift-name">Voucher chụp PSC Athena Wedding Film</div><div class="cc-gift-sub">Trị giá 500.000đ</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ 04. QUY TRÌNH ══ -->
<div class="sec-process">
  <div class="wrap">
    <div class="sec-header">
      <div class="sec-n">04</div>
      <div>
        <div class="sec-label">Quy trình</div>
        <div class="sec-title">Tham gia <em>đơn giản</em> — 4 bước</div>
      </div>
    </div>
    <div class="process-steps">
      <div class="p-step">
        <div class="p-dot"><span class="p-num">01</span></div>
        <h4>Đăng ký gói</h4>
        <p>Chọn gói hoặc nội dung và liên hệ Sora Bridal trước khi bắt đầu sản xuất.</p>
      </div>
      <div class="p-step">
        <div class="p-dot"><span class="p-num">02</span></div>
        <h4>Sản xuất nội dung</h4>
        <p>Team media Sora đồng hành từ kịch bản, quay đến dựng — đến khi hoàn thiện.</p>
      </div>
      <div class="p-step">
        <div class="p-dot"><span class="p-num">03</span></div>
        <h4>Đăng tải & gửi link</h4>
        <p>Sau khi Sora duyệt nội dung, bạn đăng tải theo yêu cầu và gửi link vào nhóm hỗ trợ.</p>
      </div>
      <div class="p-step">
        <div class="p-dot"><span class="p-num">04</span></div>
        <h4>Nhận quà tri ân</h4>
        <p>Sora xác nhận trong 24 giờ. Quà tặng / hoàn tiền trong <strong>7–14 ngày</strong> làm việc.</p>
      </div>
    </div>
    <div class="p-note">💡 Để được hỗ trợ tốt nhất và đảm bảo quyền lợi đầy đủ, bạn nhớ <strong>đăng ký với Sora trước khi bắt đầu sản xuất nội dung</strong> nhé!</div>
  </div>
</div>

<!-- ══ 05. CHÍNH SÁCH ══ -->
<section class="sec-policy">
  <div class="wrap">
    <div class="sec-header">
      <div class="sec-n">05</div>
      <div>
        <div class="sec-label">Yêu cầu & chính sách</div>
        <div class="sec-title">Tiêu chuẩn <em>& cam kết hai phía</em></div>
      </div>
    </div>
    <div class="policy-grid">
      <div class="policy-item">
        <div class="policy-icon">🚫</div>
        <div><h4>Nội dung phù hợp</h4><p>Không chứa yếu tố phản cảm, gây tranh cãi, vi phạm pháp luật hoặc ảnh hưởng tiêu cực đến hình ảnh Sora Bridal.</p></div>
      </div>
      <div class="policy-item">
        <div class="policy-icon">🏷️</div>
        <div><h4>Tag & hashtag đúng quy định</h4><p>Bài đăng phải tag tài khoản chính thức Sora Bridal và sử dụng hashtag quy định của chương trình.</p></div>
      </div>
      <div class="policy-item">
        <div class="policy-icon">👁️</div>
        <div><h4>Tài khoản công khai</h4><p>Trong thời gian nội dung đang được theo dõi, tài khoản cần để chế độ <strong>Public</strong>.</p></div>
      </div>
      <div class="policy-item">
        <div class="policy-icon">💬</div>
        <div><h4>Nội dung chân thực</h4><p>Chia sẻ cảm nhận thật — không phóng đại, không gây hiểu nhầm về sản phẩm và dịch vụ.</p></div>
      </div>
      <div class="policy-item" style="grid-column:1/-1">
        <div class="policy-icon">📅</div>
        <div><h4>Duy trì bài đăng tối thiểu 03 tháng</h4><p>Bài đăng cần giữ nguyên tối thiểu <strong>03 tháng</strong> kể từ ngày đăng tải. Xóa bài trước thời hạn đồng nghĩa từ bỏ toàn bộ quà tri ân.</p></div>
      </div>
      <div class="policy-ast">* Sora Bridal có quyền sử dụng một phần nội dung làm tư liệu truyền thông, portfolio, showcase hoặc case study thương hiệu — luôn kèm credit đầy đủ và sự trân trọng dành cho bạn.</div>
    </div>
  </div>
</section>

<!-- ══ 06. QUYỀN PHÁP LÝ ══ -->
<section class="sec-legal">
  <div class="legal-inner">
    <div class="sec-header" style="border-color:var(--border)">
      <div class="sec-n" style="color:var(--blush-mid)">06</div>
      <div>
        <div class="sec-label">Quyền pháp lý</div>
        <div class="sec-title">Quyền của <em>bạn</em> & của <em>chúng tôi</em></div>
      </div>
    </div>
    <div class="legal-cols">
      <div class="lc">
        <h4>Dâu rể — Người tạo nội dung</h4>
        <ul>
          <li>Giữ toàn quyền sở hữu nội dung gốc đã đăng tải</li>
          <li>Được thông báo nếu Sora muốn dùng nội dung ngoài phạm vi thỏa thuận</li>
          <li>Không bị yêu cầu xóa bài sau khi đã nhận quà đúng hạn</li>
          <li>Được từ chối tham gia bất kỳ lúc nào trước khi nộp nội dung</li>
          <li>Được giải thích rõ lý do nếu nội dung không được duyệt</li>
        </ul>
      </div>
      <div class="lc">
        <h4>Sora Bridal — Thương hiệu</h4>
        <ul>
          <li>Được repost, chia sẻ nội dung trên kênh thương hiệu kèm credit đầy đủ</li>
          <li>Được dùng nội dung làm tư liệu portfolio, showcase và case study</li>
          <li>Từ chối duyệt nội dung không đáp ứng tiêu chuẩn đã nêu</li>
          <li>Yêu cầu xác minh tính xác thực trước khi thanh toán quà tri ân</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ══ FOOTER ══ -->
<footer class="footer">
  <div class="footer-logo">Sora <em>Bridal</em></div>
  <div class="footer-thanks">Cảm ơn bạn đã tin tưởng và đồng hành cùng Sora Bridal —<br>mỗi khoảnh khắc của bạn là nguồn cảm hứng lớn nhất của chúng tôi.</div>
</footer>

</body>
</html>
