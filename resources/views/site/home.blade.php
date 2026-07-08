{{--
    Prayaag International School — Home (WordPress → Laravel migration, step 1)
    Self-contained public page: own <head> + inline CSS, no build step required.
    Images are hotlinked from the live site for now; download them into
    /public/images later and swap the URLs for a fully self-hosted site.
    Brand colours (navy + gold) are defined in :root below — tune to match the logo.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>PISP, Best CBSE School in Panipat | Top Schools in Samalkha</title>
    <meta name="description" content="Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Sec. Schools in Panipat. Top Schools in Samalkha.">
    <meta property="og:title" content="PISP, Best CBSE School in Panipat | Top Schools in Samalkha">
    <meta property="og:image" content="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp">
    <link rel="icon" href="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/cropped-prayaag-school-logo-270x270.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --navy:#0e2f5e; --navy-2:#15396b; --blue:#1f5aa8; --gold:#eda52a; --gold-2:#f7b733;
            --ink:#1f2937; --muted:#6b7280; --line:#e7ebf2; --soft:#f4f7fc; --white:#fff;
            --maxw:1200px; --radius:16px; --shadow:0 18px 45px rgba(14,47,94,.10);
            --serif:'Playfair Display',Georgia,serif; --sans:'Poppins',system-ui,sans-serif;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:var(--sans);color:var(--ink);line-height:1.65;background:var(--white);-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}
        .container{max-width:var(--maxw);margin:0 auto;padding:0 22px}
        .btn{display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:#3a2400;font-weight:600;
             padding:12px 22px;border-radius:999px;font-size:14.5px;transition:.2s;border:none;cursor:pointer}
        .btn:hover{background:var(--gold-2);transform:translateY(-2px)}
        .btn-navy{background:var(--navy);color:#fff}
        .btn-navy:hover{background:var(--navy-2)}
        .btn-ghost{background:transparent;border:2px solid rgba(255,255,255,.7);color:#fff}
        .btn-ghost:hover{background:#fff;color:var(--navy)}
        .section{padding:74px 0}
        .section.soft{background:var(--soft)}
        .eyebrow{color:var(--gold);font-weight:600;letter-spacing:.14em;text-transform:uppercase;font-size:12.5px}
        .stitle{font-family:var(--serif);font-size:clamp(26px,3.4vw,40px);color:var(--navy);line-height:1.15;margin:.35rem 0 .5rem}
        .ssub{color:var(--muted);max-width:680px}
        .center{text-align:center;margin-inline:auto}
        .center .ssub{margin-inline:auto}

        /* ---------- Top utility bar ---------- */
        .topbar{background:var(--navy);color:#dce6f5;font-size:13px}
        .topbar .container{display:flex;align-items:center;justify-content:space-between;gap:14px;min-height:42px;flex-wrap:wrap}
        .topbar ul{display:flex;align-items:center;gap:18px;list-style:none;flex-wrap:wrap}
        .topbar li a{display:flex;align-items:center;gap:7px;color:#dce6f5;transition:.2s}
        .topbar li a:hover{color:#fff}
        .topbar img{width:18px;height:18px;object-fit:contain;filter:brightness(0) invert(1);opacity:.9}
        .socials{display:flex;align-items:center;gap:10px}
        .socials a{width:26px;height:26px;display:grid;place-items:center;background:rgba(255,255,255,.12);border-radius:50%;transition:.2s}
        .socials a:hover{background:var(--gold)}
        .socials img{width:13px;height:13px;filter:brightness(0) invert(1)}

        /* ---------- Header / nav ---------- */
        .header{position:sticky;top:0;z-index:50;background:#fff;box-shadow:0 6px 24px rgba(14,47,94,.07)}
        .header .container{display:flex;align-items:center;justify-content:space-between;gap:18px;min-height:84px}
        .brand img{height:60px;width:auto}
        .nav{display:flex;align-items:center;gap:4px}
        .nav>ul{display:flex;align-items:center;list-style:none;gap:2px}
        .nav>ul>li{position:relative}
        .nav>ul>li>a{display:flex;align-items:center;gap:5px;padding:12px 13px;font-size:14px;font-weight:500;color:var(--ink);border-radius:8px;transition:.2s;white-space:nowrap}
        .nav>ul>li>a:hover{color:var(--navy);background:var(--soft)}
        .nav .has>a::after{content:"";width:6px;height:6px;border-right:2px solid currentColor;border-bottom:2px solid currentColor;transform:rotate(45deg) translateY(-1px);opacity:.6}
        .nav .drop{position:absolute;top:calc(100% + 6px);left:0;min-width:230px;background:#fff;border:1px solid var(--line);
                   border-radius:12px;box-shadow:var(--shadow);padding:8px;opacity:0;visibility:hidden;transform:translateY(8px);transition:.2s;list-style:none}
        .nav>ul>li:hover .drop{opacity:1;visibility:visible;transform:translateY(0)}
        .nav .drop li a{display:block;padding:9px 12px;font-size:13.5px;border-radius:8px;color:var(--ink)}
        .nav .drop li a:hover{background:var(--soft);color:var(--navy)}
        .header-badge img{height:54px;width:auto}
        .burger{display:none;background:none;border:none;cursor:pointer;flex-direction:column;gap:5px;padding:8px}
        .burger span{width:26px;height:3px;background:var(--navy);border-radius:3px;transition:.2s}

        /* ---------- Hero ---------- */
        .hero{position:relative;min-height:88vh;display:flex;align-items:center;color:#fff;
              background:linear-gradient(180deg,rgba(8,26,52,.62),rgba(8,26,52,.78)),
              url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp') center/cover no-repeat}
        .hero .container{position:relative;z-index:2}
        .hero .kicker{display:inline-block;background:rgba(237,165,42,.16);border:1px solid rgba(247,183,51,.5);
                      color:#ffd98a;padding:7px 16px;border-radius:999px;font-size:13px;font-weight:600;letter-spacing:.05em;margin-bottom:18px}
        .hero h1{font-family:var(--serif);font-size:clamp(36px,6vw,68px);line-height:1.05;font-weight:800;max-width:14ch}
        .hero p.tag{font-size:clamp(18px,2.4vw,26px);margin-top:14px;color:#e9eefb;font-weight:300;font-style:italic}
        .hero .cta{display:flex;gap:14px;margin-top:30px;flex-wrap:wrap}
        .hero .dots{position:absolute;bottom:26px;left:50%;transform:translateX(-50%);display:flex;gap:9px;z-index:2}
        .hero .dots span{width:10px;height:10px;border-radius:50%;background:rgba(255,255,255,.4)}
        .hero .dots span.on{background:var(--gold);width:26px;border-radius:6px}

        /* ---------- Quick links ---------- */
        .quick{margin-top:-58px;position:relative;z-index:5}
        .quick-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;background:#fff;border-radius:var(--radius);
                    box-shadow:var(--shadow);padding:22px}
        .qcard{display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;padding:16px 10px;border-radius:12px;transition:.2s}
        .qcard:hover{background:var(--soft);transform:translateY(-3px)}
        .qcard .ic{width:52px;height:52px;display:grid;place-items:center;border-radius:14px;
                   background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff}
        .qcard .ic svg{width:26px;height:26px}
        .qcard span{font-size:13px;font-weight:600;color:var(--navy)}

        /* ---------- Welcome (Director / Principal) ---------- */
        .welcome-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-top:34px}
        .msg{background:#fff;border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
        .msg .top{display:flex;align-items:center;gap:16px;padding:22px;background:linear-gradient(135deg,var(--navy),var(--navy-2));color:#fff}
        .avatar{width:74px;height:74px;border-radius:50%;flex:0 0 auto;display:grid;place-items:center;font-weight:700;font-size:24px;
                background:var(--gold);color:#3a2400;border:3px solid rgba(255,255,255,.5)}
        .msg .top h4{font-size:18px}
        .msg .top small{color:#cdd9ee;font-weight:500}
        .msg .body{padding:22px;color:#42505f;font-size:14.5px}
        .msg .body p+p{margin-top:12px}

        /* ---------- Testimonials ---------- */
        .tgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:34px}
        .tcard{background:#fff;border-radius:var(--radius);padding:26px;box-shadow:var(--shadow);border:1px solid var(--line);position:relative}
        .tcard .quote{font-size:54px;font-family:var(--serif);color:var(--gold);line-height:.6;height:24px}
        .tcard p{color:#48566a;font-size:14px;margin:14px 0 18px}
        .tcard .who{display:flex;align-items:center;gap:12px;border-top:1px solid var(--line);padding-top:14px}
        .tcard .who .av{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--blue));
                        color:#fff;display:grid;place-items:center;font-weight:700;font-size:15px}
        .tcard .who b{display:block;font-size:14px;color:var(--navy)}
        .tcard .who small{color:var(--muted);font-size:12.5px}

        /* ---------- News & calendar ---------- */
        .news-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:26px;margin-top:34px}
        .news-card,.cal-card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
        .card-head{background:linear-gradient(135deg,var(--navy),var(--navy-2));color:#fff;padding:18px 22px;font-weight:600;font-family:var(--serif);font-size:20px}
        .news-list{list-style:none;padding:8px 0}
        .news-list li{display:flex;gap:12px;align-items:flex-start;padding:14px 22px;border-bottom:1px solid var(--line);font-size:14.5px}
        .news-list li:last-child{border-bottom:none}
        .news-list li::before{content:"";width:9px;height:9px;border-radius:50%;background:var(--gold);margin-top:8px;flex:0 0 auto}
        .news-list a{color:var(--navy);font-weight:600}
        .cal-card{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;padding:30px;gap:14px;
                  background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff}
        .cal-card h3{font-family:var(--serif);font-size:24px}
        .cal-card p{color:#d7e2f4;font-size:14.5px}

        /* ---------- Campus ---------- */
        .campus-grid{display:grid;grid-template-columns:1fr 1fr;gap:26px;margin-top:34px}
        .wing{position:relative;border-radius:var(--radius);overflow:hidden;min-height:320px;display:flex;align-items:flex-end;
              color:#fff;box-shadow:var(--shadow)}
        .wing.jr{background:linear-gradient(180deg,rgba(14,47,94,.25),rgba(14,47,94,.85)),url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp') center/cover}
        .wing.sr{background:linear-gradient(180deg,rgba(31,90,168,.25),rgba(10,32,64,.88)),url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp') center/cover}
        .wing .inner{padding:28px}
        .wing h3{font-family:var(--serif);font-size:28px}
        .wing p{color:#dde7f6;font-size:14px;margin:6px 0 16px;max-width:42ch}

        /* ---------- Achievements timeline ---------- */
        .timeline{margin-top:40px;position:relative;padding-left:0}
        .timeline::before{content:"";position:absolute;left:120px;top:6px;bottom:6px;width:3px;background:linear-gradient(var(--gold),var(--navy))}
        .tl-row{display:grid;grid-template-columns:120px 1fr;gap:30px;margin-bottom:30px;position:relative}
        .tl-year{text-align:right;font-family:var(--serif);font-size:30px;font-weight:700;color:var(--navy);padding-top:2px}
        .tl-row::before{content:"";position:absolute;left:113px;top:12px;width:17px;height:17px;border-radius:50%;background:var(--gold);border:4px solid #fff;box-shadow:0 0 0 2px var(--gold)}
        .tl-body{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px 22px;box-shadow:0 8px 24px rgba(14,47,94,.06)}
        .tl-body ul{list-style:none}
        .tl-body li{position:relative;padding-left:20px;font-size:14px;color:#48566a;margin:6px 0}
        .tl-body li::before{content:"›";position:absolute;left:2px;color:var(--gold);font-weight:700}

        /* ---------- Life at Prayaag ---------- */
        .life-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:34px}
        .life{position:relative;border-radius:var(--radius);overflow:hidden;min-height:230px;display:flex;align-items:flex-end;color:#fff;box-shadow:var(--shadow)}
        .life .inner{padding:20px;position:relative;z-index:2}
        .life h4{font-size:19px;font-family:var(--serif)}
        .life::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent,rgba(8,24,50,.85))}
        .life.l1{background:linear-gradient(135deg,#1f5aa8,#0e2f5e)}
        .life.l2{background:linear-gradient(135deg,#e0852f,#a8430f)}
        .life.l3{background:linear-gradient(135deg,#2a9d8f,#11574f)}
        .life.l4{background:linear-gradient(135deg,#7b4bb7,#3b1d63)}

        /* ---------- Glimpses (instagram) ---------- */
        .ig-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-top:34px}
        .ig{display:block;border-radius:14px;overflow:hidden;border:1px solid var(--line);background:#fff;box-shadow:0 8px 24px rgba(14,47,94,.06);transition:.2s}
        .ig:hover{transform:translateY(-4px)}
        .ig .ph{aspect-ratio:1;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff}
        .ig .ph svg{width:40px;height:40px;opacity:.9}
        .ig .cap{padding:12px;font-size:12px;color:#48566a;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}

        /* ---------- Videos ---------- */
        .vid-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:34px}
        .vid{border-radius:14px;overflow:hidden;box-shadow:var(--shadow);background:#000;aspect-ratio:16/9}
        .vid iframe{width:100%;height:100%;border:0;display:block}

        /* ---------- Admission CTA ---------- */
        .admit{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;text-align:center;padding:64px 0}
        .admit h2{font-family:var(--serif);font-size:clamp(30px,4.5vw,52px);font-weight:800}
        .admit p{color:#d7e2f4;margin:10px 0 26px;font-size:16px}

        /* ---------- Footer ---------- */
        .footer{background:#0a2244;color:#c4d2e8;padding-top:60px;font-size:14px}
        .footer h5{color:#fff;font-family:var(--serif);font-size:18px;margin-bottom:16px;position:relative;padding-bottom:10px}
        .footer h5::after{content:"";position:absolute;left:0;bottom:0;width:42px;height:3px;background:var(--gold);border-radius:3px}
        .foot-grid{display:grid;grid-template-columns:1.3fr 1fr 1fr 1.3fr;gap:34px;padding-bottom:44px}
        .footer a:hover{color:var(--gold)}
        .footer .row{margin-bottom:10px;line-height:1.55}
        .footer .qlinks{list-style:none}
        .footer .qlinks li{margin-bottom:9px}
        .footer .qlinks li a{display:inline-flex;gap:8px}
        .footer iframe{width:100%;height:170px;border:0;border-radius:12px;filter:grayscale(.2)}
        .foot-soc{display:flex;gap:10px;margin-top:16px}
        .foot-soc a{width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.1);display:grid;place-items:center;transition:.2s}
        .foot-soc a:hover{background:var(--gold)}
        .foot-soc img{width:15px;height:15px;filter:brightness(0) invert(1)}
        .copyright{border-top:1px solid rgba(255,255,255,.12);padding:20px 0;text-align:center;color:#9fb2cf;font-size:13.5px}

        /* ---------- Responsive ---------- */
        @media(max-width:1024px){
            .quick-grid{grid-template-columns:repeat(3,1fr)}
            .tgrid{grid-template-columns:repeat(2,1fr)}
            .ig-grid{grid-template-columns:repeat(3,1fr)}
            .vid-grid{grid-template-columns:1fr 1fr}
            .foot-grid{grid-template-columns:1fr 1fr}
        }
        @media(max-width:860px){
            .burger{display:flex}
            .nav{position:fixed;inset:84px 0 auto 0;background:#fff;flex-direction:column;align-items:stretch;
                 max-height:0;overflow:hidden;transition:.3s;box-shadow:0 20px 40px rgba(0,0,0,.12);border-top:1px solid var(--line)}
            .nav.open{max-height:80vh;overflow-y:auto;padding:10px 0}
            .nav>ul{flex-direction:column;align-items:stretch;gap:0;width:100%}
            .nav>ul>li>a{padding:14px 22px;justify-content:space-between}
            .nav .drop{position:static;opacity:1;visibility:visible;transform:none;box-shadow:none;border:none;
                      max-height:0;overflow:hidden;padding:0;transition:.25s;background:var(--soft);border-radius:0}
            .nav>ul>li.show .drop{max-height:500px;padding:6px 0 10px}
            .nav .drop li a{padding-left:38px}
            .welcome-grid,.news-grid,.campus-grid{grid-template-columns:1fr}
            .topbar ul.util{display:none}
        }
        @media(max-width:620px){
            .section{padding:54px 0}
            .quick-grid{grid-template-columns:repeat(2,1fr)}
            .tgrid,.life-grid,.ig-grid,.vid-grid{grid-template-columns:1fr}
            .timeline::before{left:8px}
            .tl-row{grid-template-columns:1fr;gap:8px;padding-left:34px}
            .tl-year{text-align:left}
            .tl-row::before{left:1px}
            .foot-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

@php
    $img = 'https://prayaaginternationalschool.com/wp-content/uploads/';
    $nav = [
        ['Home', '/', []],
        ['About Us', '/about-us/', []],
        ['Admission Process', '/admissions/', [
            ['Admissions','/admissions/'], ['Fee Structure','/fee-structure/'],
        ]],
        ['Our Campus', '#', [
            ['Junior Wing','/junior-wing-school-in-panipat/'], ['Senior Wing','/senior-wing-school-in-panipat/'],
        ]],
        ['Facilities', '/classrooms/', [
            ['Classrooms','/classrooms/'], ['Labs','/labs/'], ['Library','/library/'], ['Sports','/sports/'],
            ['Transportations','/transportations/'], ['Safety & Security','/safety-security/'],
            ['Tours and Excursions','/tours-and-excursions/'], ['UNESCO','/unesco/'],
        ]],
        ['Events', '/events/', []],
        ['Alumni', '/alumni/', []],
        ['Academic Downloads', '/downloads/', []],
        ['Media', '/media/', []],
        ['Contact Us', '/contact-us/', []],
    ];
    $testimonials = [
        ['Choosing Prayaag International School for our child has been one of the best decisions we made as parents. The school’s caring and supportive staff, excellent infrastructure, and consistent academic achievements make it the top choice in Panipat.','Mr. Kamal Chuge','F/O Felix VII H'],
        ['Prayaag International School’s commitment to holistic education is commendable. The school’s focus on sports, arts, and cultural activities, along with academics, ensures that children receive a well-balanced education that prepares them for life.','Ms. Rajni','M/O Diksha IX Z'],
        ['I can confidently say that Prayaag International School is the best school in Panipat for promoting a love for learning. The teachers create an engaging and stimulating environment, fostering a passion for knowledge among the students.','Ms. Neelam','M/O Bhumi IX H'],
        ['Prayaag International School has a fantastic support system for students with different abilities. The inclusive approach and personalized attention given to each child make it a top school in Panipat for catering to diverse learning needs.','Mr. Pratap Singh','F/O Devika XI H'],
        ['We are extremely satisfied with our decision to enroll our child in Prayaag International School. The school’s strong focus on values, ethics, and discipline, combined with excellent academic programs, makes it the best choice in Panipat.','Mr. Vishal Garg','F/O Shalok Garg X P'],
        ['Prayaag International School provides a well-rounded education that prepares children for the future. The school’s commitment to innovation, technology integration, and experiential learning makes it stand out among the top schools in Panipat.','Ms. Pinki','M/O Shelly IX H'],
    ];
    $achievements = [
        ['2016', ['Laid the foundation stone of the school']],
        ['2019', ['District Level Karate Competition','Wrestling Competition (Block level)','Capacity Building Programme By CBSE','Annual Function – Let Me Fly']],
        ['2020', ['Vidya Mandir Quest – Biggest National Level Quiz','British Council International School Award','Go Green Initiative']],
        ['2021', ['National Level Karate Championship','Building Resilience – A virtue in Covid Times','Dhammika KAT Cup Championship','Sports Tournament – District Level','Faculty / Staff Sports Tournament','State Level Painting Competition']],
        ['2022', ['Celebration of World Environment Day','Excursions – Explore by Yourself','Fireless Cooking – Experiential Learning','Stellar Board Results (2021-22)','SOF Olympiad Results','Christmas Carnival & Baisakhi Mela']],
        ['2023', ['Educational Trip to Top Ranked University','Participated in Rahgiri','District Senior Wushu Championship','National India Open Shooting Championship','District Swimming Championship','Nukkad Natak, Run for Victory, Veer Bal Diwas','Geeta Mahotsav & Career Counselling']],
    ];
    $glimpses = [
        ['Confidence grows when little voices are heard — a fun-filled Show and Tell Competition for Grade II.','https://www.instagram.com/reel/DZHqOv6sraX/'],
        ['Science faculty of the vicinity attended the CBSE Science Workshop at Prayaag International School.','https://www.instagram.com/p/DZE4rOxDwlz/'],
        ['Too cute to handle, too fun to miss! Cake vibes, happy tribe, and tiny smiles all day long.','https://www.instagram.com/p/DZCWLHUDwCR/'],
        ['“Science is the key to endless possibilities.” Young minds at the Science Quiz Competition.','https://www.instagram.com/reel/DY9jPe9Mv_V/'],
        ['Eid Mubarak! Wishing our entire school family a joyful celebration filled with togetherness.','https://www.instagram.com/p/DY3mRgTj-oB/'],
    ];
    function initials($name){ $p=preg_split('/\s+/',trim($name)); $a=mb_substr($p[0]??'',0,1); $b=mb_substr(end($p)??'',0,1); return strtoupper($a.$b); }
@endphp

{{-- ===================== TOP BAR ===================== --}}
<div class="topbar">
    <div class="container">
        <ul class="util">
            <li><a href="#"><img src="{{ $img }}2022/01/school-png-icon-150x150.png" alt="">CBSE Affiliation No. : 531592</a></li>
            <li><a href="#"><img src="{{ $img }}2022/01/school-bag-png-Icon-150x150.png" alt="">School Code : 41568</a></li>
            <li><a href="http://prayaag.accevate.com/"><img src="{{ $img }}2022/01/student-login-icon-150x150.png" alt="">Student Login</a></li>
            <li><a href="http://prayaag.accevate.com/admin/"><img src="{{ $img }}2022/01/Admin-Login-150x150.png" alt="">Admin Login</a></li>
            <li><a href="https://pisp.accevate.com/online/main"><img src="{{ $img }}2022/01/Online-Payment-150x150.png" alt="">Online Payment</a></li>
        </ul>
        <div class="socials">
            <a href="https://www.facebook.com/PrayaagInternationalSchoolPanipat" aria-label="Facebook"><img src="{{ $img }}2023/08/facebook-social-icon.png" alt="Facebook"></a>
            <a href="http://instagram.com/prayaag2016" aria-label="Instagram"><img src="{{ $img }}2023/08/instagram-social-icon.png" alt="Instagram"></a>
            <a href="https://twitter.com/MailusIntl" aria-label="Twitter"><img src="{{ $img }}2023/08/x-social-icon.png" alt="Twitter"></a>
            <a href="https://www.linkedin.com/company/prayaag-international-school" aria-label="LinkedIn"><img src="{{ $img }}2023/08/linkedin-social-icon.png" alt="LinkedIn"></a>
            <a href="https://www.youtube.com/channel/UCeqR_-8SsGfMi09aX1FSzdA" aria-label="YouTube"><img src="{{ $img }}2023/08/youtube-social-icon.png" alt="YouTube"></a>
        </div>
    </div>
</div>

{{-- ===================== HEADER / NAV ===================== --}}
<header class="header">
    <div class="container">
        <a class="brand" href="/"><img src="{{ $img }}2021/12/prayaag-school-logo.png" alt="Prayaag International School, Panipat"></a>

        <nav class="nav" id="nav">
            <ul>
                @foreach ($nav as [$label, $href, $children])
                    <li class="{{ $children ? 'has' : '' }}">
                        <a href="{{ $href }}">{{ $label }}</a>
                        @if ($children)
                            <ul class="drop">
                                @foreach ($children as [$clabel, $chref])
                                    <li><a href="{{ $chref }}">{{ $clabel }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <a class="header-badge" href="https://prayaaginternationalschool.com/" aria-label="British Council">
            <img src="{{ $img }}2022/01/british-council-logo-150x150.jpg" alt="British Council">
        </a>

        <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
</header>

{{-- ===================== HERO ===================== --}}
<section class="hero">
    <div class="container">
        <span class="kicker">★ Admission Open 2026-27</span>
        <h1>Prayaag International School, Panipat</h1>
        <p class="tag">Life begins here…</p>
        <div class="cta">
            <a class="btn" href="https://pisp.accevate.com/registration/">Online Registration →</a>
            <a class="btn btn-ghost" href="/about-us/">Discover the School</a>
        </div>
    </div>
    <div class="dots"><span class="on"></span><span></span><span></span><span></span><span></span><span></span></div>
</section>

{{-- ===================== QUICK LINKS ===================== --}}
<div class="container quick">
    <div class="quick-grid">
        @php
            $quick = [
                ['School Trip','/tours-and-excursions/','M3 7h13l3 4v6h-3a2 2 0 0 1-4 0H8a2 2 0 0 1-4 0H3V7z'],
                ['Labs','/labs/','M9 3v6l-5 9a2 2 0 0 0 2 3h12a2 2 0 0 0 2-3l-5-9V3M9 3h6M7.5 14h9'],
                ['Sports','/sports/','M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18'],
                ['Library','/library/','M4 5a2 2 0 0 1 2-2h6v16H6a2 2 0 0 0-2 2V5zM20 5a2 2 0 0 0-2-2h-6v16h6a2 2 0 0 1 2 2V5z'],
                ['Classroom','/classrooms/','M3 5h18v11H3zM3 16l-1 3h20l-1-3M9 9h6'],
                ['Safety & Security','/safety-security/','M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4'],
                ['Transportation','/transportations/','M3 6h18v8H3zM3 14l1 4h2l1-2h10l1 2h2l1-4M7 18a1.5 1.5 0 1 0 0-.01M17 18a1.5 1.5 0 1 0 0-.01'],
                ['UNESCO','/unesco/','M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3v18'],
                ['Events','/events/','M3 5h18v16H3zM3 9h18M8 3v4M16 3v4'],
                ['Photo Gallery','/media/','M3 5h18v14H3zM3 15l5-5 4 4 3-3 6 6'],
            ];
        @endphp
        @foreach ($quick as [$lbl, $href, $path])
            <a class="qcard" href="{{ $href }}">
                <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg></span>
                <span>{{ $lbl }}</span>
            </a>
        @endforeach
    </div>
</div>

{{-- ===================== WELCOME ===================== --}}
<section class="section">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Welcome to Prayaag</span>
            <h2 class="stitle">A Message from Our Leadership</h2>
            <p class="ssub">Nurturing young minds and shaping the leaders of tomorrow through a shared commitment between dedicated teachers, motivated students and supportive parents.</p>
        </div>
        <div class="welcome-grid">
            <div class="msg">
                <div class="top">
                    <div class="avatar">AG</div>
                    <div><h4>Mrs. Anju Gupta</h4><small>Director</small></div>
                </div>
                <div class="body">
                    <p>As we continue our journey to nurture young minds and shape the leaders of tomorrow, we are delighted to connect with you and share our vision. At Prayaag International School, we believe education is a shared commitment between dedicated teachers, motivated students and supportive parents.</p>
                    <p>Our mission is to provide a dynamic and nurturing environment where every child can discover their unique potential and develop into a confident, responsible and well-rounded individual — blending academic excellence with creativity, critical thinking and a strong sense of community.</p>
                    <p>Thank you for entrusting us with your child’s education. We look forward to a successful and fulfilling academic year ahead.</p>
                </div>
            </div>
            <div class="msg">
                <div class="top">
                    <div class="avatar">MS</div>
                    <div><h4>Mrs. Mamta Sachdeva</h4><small>Principal</small></div>
                </div>
                <div class="body">
                    <p>At Prayaag International School, education is treated as a serious trust, not a transaction. The school is committed to deep, disciplined learning rooted in Indian cultural values while equipping students with essential 21st-century competencies.</p>
                    <p>In a world shaped by artificial intelligence, we adopt a measured and vigilant approach — guiding students to treat AI as an aid, not a substitute, to uphold academic honesty, and to think, write and solve independently in a technology-rich environment.</p>
                    <p>With a purposeful partnership with parents, we aim to stand apart as an institution defined by preparing resilient, capable individuals who can help run and renew an ever-changing world.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== TESTIMONIALS ===================== --}}
<section class="section soft">
    <div class="container">
        <div class="center">
            <span class="eyebrow">In Their Words</span>
            <h2 class="stitle">Parents Testimonials</h2>
        </div>
        <div class="tgrid">
            @foreach ($testimonials as [$quote, $name, $role])
                <div class="tcard">
                    <div class="quote">“</div>
                    <p>{{ $quote }}</p>
                    <div class="who">
                        <div class="av">{{ initials($name) }}</div>
                        <div><b>{{ $name }}</b><small>{{ $role }}</small></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== NEWS & CALENDAR ===================== --}}
<section class="section">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Events &amp; News</span>
            <h2 class="stitle">News From The Campus</h2>
        </div>
        <div class="news-grid">
            <div class="news-card">
                <div class="card-head">Latest Updates</div>
                <ul class="news-list">
                    <li><a href="{{ $img }}2022/03/SCHOLARSHIP-EXAM-1.pdf">Scholarship Exam Results</a></li>
                    <li>Online career counselling drive for Grade IX to XII</li>
                    <li>Regular parents counselling</li>
                    <li>Regular student counselling</li>
                    <li>Vaccination Drive for age 15 to 18</li>
                </ul>
            </div>
            <div class="cal-card">
                <h3>Check the Full Calendar for Upcoming Events</h3>
                <p>Stay up to date with everything happening across the Prayaag campus this academic year.</p>
                <a class="btn" href="/events/">Upcoming Events →</a>
            </div>
        </div>
    </div>
</section>

{{-- ===================== CAMPUS ===================== --}}
<section class="section soft">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Our Campus</span>
            <h2 class="stitle">The Place Where Beginners Become the Greatest</h2>
            <p class="ssub">Two dedicated wings designed for every stage of a child’s growth.</p>
        </div>
        <div class="campus-grid">
            <div class="wing jr"><div class="inner"><h3>Junior Wing</h3><p>A joyful, safe and stimulating world where curiosity is sparked and the foundations of learning are laid.</p><a class="btn" href="/junior-wing-school-in-panipat/">Explore Junior Wing →</a></div></div>
            <div class="wing sr"><div class="inner"><h3>Senior Wing</h3><p>An environment of academic rigour, leadership and character that prepares students for the world ahead.</p><a class="btn" href="/senior-wing-school-in-panipat/">Explore Senior Wing →</a></div></div>
        </div>
    </div>
</section>

{{-- ===================== ACHIEVEMENTS ===================== --}}
<section class="section">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Our Achievements</span>
            <h2 class="stitle">Milestones of Our Journey</h2>
            <p class="ssub">Every day is a stepping stone — here are some of the finest steps we have taken.</p>
        </div>
        <div class="timeline">
            @foreach ($achievements as [$year, $items])
                <div class="tl-row">
                    <div class="tl-year">{{ $year }}</div>
                    <div class="tl-body"><ul>@foreach ($items as $it)<li>{{ $it }}</li>@endforeach</ul></div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== LIFE AT PRAYAAG ===================== --}}
<section class="section soft">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Life at Prayaag</span>
            <h2 class="stitle">Celebrating Every Moment</h2>
            <p class="ssub">We celebrate the tiniest ounce of happiness in the grandest way possible.</p>
        </div>
        <div class="life-grid">
            <a class="life l1" href="/media/"><div class="inner"><h4>Dance</h4></div></a>
            <a class="life l2" href="/media/"><div class="inner"><h4>Sports</h4></div></a>
            <a class="life l3" href="/media/"><div class="inner"><h4>Fun Activities</h4></div></a>
            <a class="life l4" href="/media/"><div class="inner"><h4>Art &amp; Craft</h4></div></a>
        </div>
    </div>
</section>

{{-- ===================== GLIMPSES (INSTAGRAM) ===================== --}}
<section class="section">
    <div class="container">
        <div class="center">
            <span class="eyebrow">@prayaag2016</span>
            <h2 class="stitle">Glimpses of Prayaag</h2>
        </div>
        <div class="ig-grid">
            @foreach ($glimpses as [$cap, $url])
                <a class="ig" href="{{ $url }}" target="_blank" rel="noopener">
                    <div class="ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></div>
                    <div class="cap">{{ $cap }}</div>
                </a>
            @endforeach
        </div>
        <div class="center" style="margin-top:30px"><a class="btn btn-navy" href="https://www.instagram.com/prayaag2016/">Follow on Instagram</a></div>
    </div>
</section>

{{-- ===================== VIDEOS ===================== --}}
<section class="section soft">
    <div class="container">
        <div class="center">
            <span class="eyebrow">Watch</span>
            <h2 class="stitle">From Our Channel</h2>
        </div>
        <div class="vid-grid">
            <div class="vid"><iframe src="https://www.youtube.com/embed/uF-rgUjsTEE" title="Prayaag video" allowfullscreen loading="lazy"></iframe></div>
            <div class="vid"><iframe src="https://www.youtube.com/embed/R1RxRZRUEa0" title="Prayaag video" allowfullscreen loading="lazy"></iframe></div>
            <div class="vid"><iframe src="https://www.youtube.com/embed/JdZbM6x7Y8s" title="Prayaag video" allowfullscreen loading="lazy"></iframe></div>
        </div>
    </div>
</section>

{{-- ===================== ADMISSION CTA ===================== --}}
<section class="admit" id="top">
    <div class="container">
        <h2>Admission Open 2026-27</h2>
        <p>Give your child the Prayaag advantage. Register online today.</p>
        <a class="btn" href="https://pisp.accevate.com/registration/">Online Registration →</a>
    </div>
</section>

{{-- ===================== FOOTER ===================== --}}
<footer class="footer">
    <div class="container">
        <div class="foot-grid">
            <div>
                <h5>Address</h5>
                <div class="row">Prayaag International School, Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana</div>
                <h5 style="margin-top:22px">Phone No</h5>
                <div class="row"><a href="tel:9350748851">+91 93507 48851</a><br>+91 180-2565555, 2575555</div>
                <div class="foot-soc">
                    <a href="https://www.facebook.com/PrayaagInternationalSchoolPanipat"><img src="{{ $img }}2023/08/facebook-social-icon.png" alt="Facebook"></a>
                    <a href="http://instagram.com/prayaag2016"><img src="{{ $img }}2023/08/instagram-social-icon.png" alt="Instagram"></a>
                    <a href="https://twitter.com/MailusIntl"><img src="{{ $img }}2023/08/x-social-icon.png" alt="Twitter"></a>
                    <a href="https://www.linkedin.com/company/prayaag-international-school"><img src="{{ $img }}2023/08/linkedin-social-icon.png" alt="LinkedIn"></a>
                    <a href="https://www.youtube.com/channel/UCeqR_-8SsGfMi09aX1FSzdA"><img src="{{ $img }}2023/08/youtube-social-icon.png" alt="YouTube"></a>
                </div>
            </div>
            <div>
                <h5>Opening Hours</h5>
                <div class="row">Mon – Sat : 08:00 AM – 03:30 PM</div>
                <div class="row">Sunday : Closed</div>
            </div>
            <div>
                <h5>Quick Links</h5>
                <ul class="qlinks">
                    <li><a href="/top-10-schools-in-panipat/">Top Schools in Panipat</a></li>
                    <li><a href="/best-schools-in-samalkha/">Best Schools in Samalkha</a></li>
                    <li><a href="/best-pre-nursery-school-in-panipat/">Best Pre Nursery School</a></li>
                    <li><a href="/disclosure/">Disclosure</a></li>
                    <li><a href="/book-list/">Book List</a></li>
                    <li><a href="/career/">Career</a></li>
                    <li><a href="/media/">Media</a></li>
                </ul>
            </div>
            <div>
                <h5>Our Location</h5>
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13914.976184424926!2d76.986936!3d29.3191828!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1640849540342!5m2!1sen!2sin" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
    <div class="copyright">Copyright {{ date('Y') }} © <strong style="color:#fff">Prayaag International School</strong></div>
</footer>

<script>
    // Mobile menu + dropdown toggles
    const burger = document.getElementById('burger');
    const nav = document.getElementById('nav');
    burger.addEventListener('click', () => nav.classList.toggle('open'));

    document.querySelectorAll('.nav .has > a').forEach(a => {
        a.addEventListener('click', (e) => {
            if (window.innerWidth <= 860) {
                e.preventDefault();
                a.parentElement.classList.toggle('show');
            }
        });
    });

    // Hero dot animation (purely visual, content is a single hero)
    const dots = document.querySelectorAll('.hero .dots span');
    if (dots.length) {
        let i = 0;
        setInterval(() => {
            dots.forEach(d => d.classList.remove('on'));
            i = (i + 1) % dots.length;
            dots[i].classList.add('on');
        }, 2800);
    }
</script>
</body>
</html>
