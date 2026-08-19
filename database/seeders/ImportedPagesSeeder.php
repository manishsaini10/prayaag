<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Imported pages from the Prayaag International School WordPress export.
 * Each page keeps its ORIGINAL slug + a Custom HTML widget holding the
 * converted content. Images reference the live wp-content URLs.
 *
 *   php artisan db:seed --class=Database\\Seeders\\ImportedPagesSeeder
 */
class ImportedPagesSeeder extends Seeder
{
    public function run(): void
    {
        $tree = app(PageTreeService::class);
        $renderer = app(PageRenderer::class);
        foreach ($this->pages() as $p) {
            $page = Page::firstOrCreate(['slug' => $p['slug']], ['title' => $p['title'], 'status' => 'published']);
            $page->update([
                'title'  => $p['title'],
                'status' => 'published',
                'seo'    => array_filter(['title' => $p['seo_title'] ?? '', 'description' => $p['seo_desc'] ?? '']),
            ]);
            $widgets = [['type' => 'html', 'settings' => ['html' => $p['html']]]];
            if (!empty($p['form'])) { $widgets[] = ['type' => 'contact_form', 'settings' => []]; }
            $sections = [['type' => 'section', 'rows' => [['columns' => [['width' => 12, 'widgets' => $widgets]]]]]];
            $tree->sync($page, $sections);
            $renderer->forget($page);
        }
    }

    private function pages(): array
    {
        return [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<h2>Who we are</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>Our website address is: https://prayaaginternationalschool.com.</p><h2>Comments</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p><p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p><h2>Media</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p><h2>Cookies</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p><p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p><p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p><p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p><h2>Embedded content from other websites</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p><p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p><h2>Who we share your data with</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p><h2>How long we retain your data</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p><p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p><h2>What rights you have over your data</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p><h2>Where we send your data</h2><p><strong class="privacy-policy-tutorial">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p>
PISPHTML,
            ],
            [
                'slug' => 'best-school-in-panipat',
                'title' => 'Home',
                'seo_title' => 'PISP, Best CBSE School in Panipat | Top Schools in Samalkha',
                'seo_desc' => 'Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Sec. Schools in Panipat. Top Schools in Samalkha.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-Internaitional-Campus.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase alt-font"><span style="color: #f99b1c; font-family: 'book antiqua', palatino; background-color: 	#80808088;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/02/Play-ground.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="color: #f99b1c; font-family: 'book antiqua', palatino;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-School-Morning-Assembly.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="color: #f99b1c; font-family: 'book antiqua', palatino;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/02/student-playing-football.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="color: #f99b1c; font-family: 'book antiqua', palatino;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/02/swimming-classes.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="color: #f99b1c; font-family: 'book antiqua', palatino;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/02/Student-teaching.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="color: #f99b1c; font-family: 'book antiqua', palatino;"><strong>Prayaag International school, Panipat</strong></span></h2>
<p>Life begins here...</p>

</div></div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/school_trip_prayaag_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">School Trip</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/school_labs_prayaag_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Labs</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/sports_school_prayaag_int_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Sports</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/libraryl_prayaag_int_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Library</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/classroom_prayaag_int_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Classroom</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/security_trip_prayaag_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Safety &amp; Security</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/transport_prayaag_int_school_panipat.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Transportation</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/unesco_prayaag_int_school_panipat_01.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">UNESCO</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/10/Events.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Events</span></h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/10/Photo_Gallery.jpg" alt="" style="width:44px;height:auto;display:inline-block">

<h3><span style="color: #f99b1c;">Photo Gallery</span></h3>

</div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c; font-family: 'book antiqua', palatino;">Welcome Prayaag</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/07/Prayaag-International-school-panipat-principal.webp" alt="" loading="lazy">

<h4><span style="color: #ed1c24;"><strong>Mrs. Anju Gupta (Director)</strong></span></h4>
<p><span style="color: #000000;">As we continue our journey to nurture young minds and shape the leaders of tomorrow , we are delighted to have this opportunity to connect with you and share our vision.<br />
At Prayaag International School , we believe that education is a shared commitment between dedicated teachers , motivated students and supportive parents .Our mission is to provide a dynamic and nurturing environment where every child can discover his unique potential and develop into a confident , resposible and well - rounded individual.<br />
We are committed to fostering a love for learning by blending academic excellence with a focus on creativity , critical thinking and a strong sense of community.</span></p>
<p><span style="color: #000000;">Our dedicated faculty is passionate about creating an engaging and enriching learning experience that prepares students for the challenges and opportunities of the future . We strive to cultivate not only academic knowledge but also the essential life skills and moral principles that will guide them throughout their lives.<br />
We believe that education is a shared commitment. The dedication of our exceptional faculty and staff , coupled with the unwavering support and partnership of our parents , forms the cornerstone of our students' success. We value this collaboration and encourage open communication to ensure every child feels valued , supported and encouraged to reach his full potential.<br />
Thank you for enstruting us with your child's education.We look forward to a successful and fulfilling academic year ahead.</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/01/Mamta_Sachdeva_Principal.webp" alt="" loading="lazy">

<h4><span style="color: #ed1c24;"><strong>Mrs. Mamta Sachdeva (Principal)</strong></span></h4>
<p style="color: #000000;">At Prayaag International School, education is treated as a serious trust, not a transaction. The School is committed to deep, disciplined learning rooted in Indian cultural values while equipping students with essential 21st century competencies. In a world shaped by artificial intelligence, the school adopts a measured and vigilant approach with guidance to treat AI as an aid, not a substitute; to question outputs, uphold academic honesty, build resilience to think, write and solve independently in a technology-rich environment.<br />This commitment is implemented through an academic leadership, a rigorously selected and professionally developed faculty, structured and transparent academic systems, and classrooms that prioritise understanding over rote, application over display, and most importantly character over convenience.<br />With purposeful and strong partnership with parents, we aim to stand apart as an institution defined by preparing resilient future resources and capable individuals who can help run and renew an ever changing world.</p>

<h2 style="text-align: center;"><span style="color: #f99b1c; font-size: 150%; font-family: 'book antiqua', palatino;">Events and News</span></h2>

<h2><span style="color: #000000; font-size: 120%;">News From The Campus</span></h2>

<p><marquee direction="up" scrollamount="2"> </p>
<ul>
<li><a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/03/SCHOLARSHIP-EXAM-1.pdf"><strong><span style="color: #000000;">Scholarship Exam Results</span></strong></a></li>
<li><span style="color: #000000;">Online carrier counselling drive for grade Ix to XII</span></li>
<li><span style="color: #000000;">Regular parents counselling</span></li>
<li><span style="color: #000000;">Regular student counselling</span></li>
<li><span style="color: #000000;">Vaccination Drive for age 15 to 18</span></li>
</ul>
<p></marquee> </p>

<h2><span style="color: #000000; font-size: 120%;">Check The Full Calendar for Upcoming Events</span></h2>

<a class="imp-btn" href="https://prayaaginternationalschool.com/events/">Upcoming Events</a>

<h2><span style="color: #f99b1c; font-size: 150%; font-family: 'book antiqua', palatino;">Our Campus</span></h2>

<p style="text-align: center;"><span style="font-weight: 400; color: #000000;">The place where beginners were on their way to become greatest. </span></p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/junior-wing/">Junior Wing</a>

<a class="imp-btn" href="https://prayaaginternationalschool.com/senior-wing/">Senior Wing</a>

<h2><span style="color: #f99b1c; font-size: 150%; font-family: 'book antiqua', palatino;">Our Achievements</span></h2>

<p style="text-align: center;"><span style="font-weight: 400; color: #000000;">Every day is like a stepping stone in this journey but here are some of the finest steps that we took.</span></p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/compass.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3>2016</h3>

<ul>
<li><span style="font-weight: 400;"><span style="color: #000000;">led the foundation stone of the school</span> </span></li>
</ul>

<h3>2019</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">District Level Karate Competition </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Wrestling Competition (Block level)</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Capacity Building Programme By CBSE </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Annual Function- Let Me Fly</span> </span></li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/canvas.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/paper.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3>2020</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Vidya Mandir Quest- Biggest National Level Quiz </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">British Council International School Award </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Go Green Initiative</span> </span></li>
</ul>

<h3>2021</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">National Level Karate Championship</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;"> Building Resilience- A virtue in Covid Times </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Dhammika KAT Cup Championship </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Sports Tournament- District Level </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Faculty/ Staff Sports Tournament (Why should students have all the fun?)</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">State Level Painting Competition</span> </span></li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/02/pallete.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/paper.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3>2022</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">A Step Towards Sustainable Environment-Celebration of World Environment Day </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Excursions- Explore by Yourself </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Fireless Cooking -Experiential Learning</span> </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Stellar Performance- Board Results (2021-22) </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">SOF Olympiad Results </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Sports Tournaments </span> </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Counseling Sessions-Career Counseling, HCL Seminar </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Christmas Carnival Organised - a Whirl of Fun and Excitement </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Vibrant Celebration of Culture and Tradition - Baisakhi Mela Celebrated </span> </span></li>
</ul>

<h3>2023</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Educational Trip to Top Ranked University </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Participated in Rahgiri </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">District Senior W:USHU Championship </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">National India Open Shooting Championship </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">District Swimming Championship </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Haryana School Games Federation of India Canvas Art Contest </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Nukkad Natak </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Run for Victory </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Veer Bal Diwas</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Geeta Mahotsav </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Legal Awareness Counselling Session </span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Career Counselling Session </span></li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/02/pallete.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h2><span style="color: #f99b1c; font-size: 150%; font-family: 'book antiqua', palatino;">Life at Prayaag</span></h2>

<p style="text-align: center;"><span style="font-weight: 400; color: #000000;">At Prayaag, we always made sure to celebrate tiniest ounce of happiness in grandest way possible </span></p>

<p>Dance</p>

<p>Sports</p>

<p>Fun Activites</p>

<p>Art & Craft</p>

<h2 style="text-align: center;"><span style="color: #f99b1c; font-size: 150%; font-family: 'book antiqua', palatino;">Glimpses of Prayaag</span></h2>

<iframe src="https://www.facebook.com/plugins/video.php?height=314&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F253105543457607%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>

<iframe src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F963896641178047%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>

<p><iframe src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1694315614072401%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe></p>

<iframe src="https://www.facebook.com/plugins/video.php?height=316&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1252873791879404%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
PISPHTML,
            ],
            [
                'slug' => 'junior-wing-school-in-panipat',
                'title' => 'Junior Wing',
                'seo_title' => 'Junior Wing school, Nursery School, Kindergarten in Panipat',
                'seo_desc' => 'Discover excellence in early education at PISP Junior Wing School, Panipat premier nursery school, kindergarten, and preschool. Enroll today!',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-Internation-Play-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase" style="text-align: center;"><strong>Junior Wing School</strong></h3>
<p style="text-align: center;"><span style="font-weight: 400;">We at Prayaag, believe that initial days at school bring the best out of young learners. Therefore we try to emphasize the overall growth of a child</span></p>

</div></div>

<h6>Best place to start</h6>

<h2><span style="font-size: 150%; color: #f99b1c;">Junior Wing School</span></h2>

<p><span style="color: #000000;">To sow and nurture the seeds of wisdom in the formative years of education, the <a href="https://prayaaginternationalschool.com/junior-wing/">Junior Wing</a> of Prayaag International School, Panipat consists of highly-educated and experienced teachers to inculcate the foundation of contemporary education.  We strive to provide an environment that helps in building a child's body, mind and soul. Therefore, the junior wing has its own self-contained building with Smart Classrooms, well-equipped Library, Play-Area, Music Room and Activity Room. Every child's well-being and safety is ensured by our 360 degree surveillance and well-trained non-teaching staff. A world class teaching pedagogy is used to cater to different intelligences, such as hands-on activities, group projects, visuals aids, story telling, role play and logical reasoning tasks. Separate play area is endowed with creative tools, toys and soft floor to provide opportunities of exploration and development of kinesthetic skills. Activities and Competitions are held throughout the year across all disciplines to promote social-skill and artistic development. A low teacher-to-student ratio is maintained to promote a personalized approach towards the needs and development of every student.</span></p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Student-Yoga-Practice.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Yoga-Teaching-prayaag-International.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-School-Laibrary.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Faculties</span></h2>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>
PISPHTML,
            ],
            [
                'slug' => 'senior-wing-school-in-panipat',
                'title' => 'Senior Wing',
                'seo_title' => 'Senior Wing School | Senior Secondary School in Panipat',
                'seo_desc' => 'Senior Wing School, Best Senior Secondary School, Senior Secondary School in Panipat, Best CBSE Schools in Samalkha, Top CBSE School In Panipat',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Scatting-Practice.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Senior wing School</strong></h3>
<p><span style="font-weight: 400;">Teenage life plays a crucial role in shaping a person’s character, vision and personality. And that's what our goal is, to provide students with every possible instrument that will help them in becoming a better human.</span></p>

</div></div>

<h6>Best place to start</h6>

<h2><span style="font-size: 150%; color: #f99b1c;">Senior Wing School</span></h2>

<p><span style="color: #000000;">Education is imparted through Tech-enabled Classrooms, well-enhanced and upgraded Science, Computer and Language Laboratories. An amalgamation of modernity, refinement, culture, and discipline is what we impart to our students. Our highly-accomplished teaching staff thrives to metamorphose the students into fervent global citizens who are confident, responsible and fearless. Our holistic pedagogy aims at unleashing each student's potential to enkindle his/her originality and nurture the zeal for achieving what one is focused upon. By providing the opportunities of practical and experiential learning, we make certain that our students are driven beyond the rigid structures of classroom learning and present new and sustainable ideas. At Prayaag International School, Panipat, we don't only focus on the outstanding results in various disciplines, but also focusses on fostering the overall well-being and holistic success of students. Prayaag International School's <a href="https://prayaaginternationalschool.com/senior-wing-school-in-panipat/">Senior Wing</a> stands as a beacon of academic excellence in Panipat.</span></p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Chess-match-between-students.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Students-Playing-Cricket.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Basketball-Practice.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Faculties</span></h2>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>

<h4>Mr. C.K. Sharma</h4>
<p>Chairman</p>
PISPHTML,
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => true,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Admin-Resception.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Reach Us</strong></h3>
<p>Contact us for any of your querry</p>

</div></div>

<h2><span style="color: #f99b1c; font-size: 150%;">Contact</span></h2>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/mail-fill.png" alt="" style="width:44px;height:auto;display:inline-block">

<h4>Send us an email</h4>
<p><a title="Prayaag International School Email-Id" href="mailto:mailus@pisp.in">mailus@pisp.in</a></p>

</div>
<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/map-pin-2-fill.png" alt="" style="width:44px;height:auto;display:inline-block">

<h4>Visit our School</h4>
<p data-line-height="m">Prayaag International School<br />Opp. New Police Lines<br />Near Indraprastha Institute of Medical Sciences<br /> NH-44, Panipat-132103, Haryana</p>

</div>
<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/phone-fill.png" alt="" style="width:44px;height:auto;display:inline-block">

<h4>Call us</h4>
<p>+<a href="tel:919350748851">91 9350748851</a>, <a title="Prayaag International School Landline" href="tel:01802565555">+91 180-2565555</a>,&nbsp;<a title="Prayaag International School-Contact" href="tel:01802575555">2575555</a></p>

</div>

<h2><span style="color: #f99b1c; font-size: 150%;">Follow us</span></h2>

<iframe src="https://www.facebook.com/plugins/video.php?height=314&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F253105543457607%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>

<iframe src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F963896641178047%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>

<p><iframe src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1694315614072401%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe></p>

<iframe src="https://www.facebook.com/plugins/video.php?height=316&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1252873791879404%2F&amp;show_text=true&amp;width=560&amp;t=0" width="560" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>

<h2><span style="font-size: 150%;">Let's connect</span></h2>

<p><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d55659.904628583856!2d76.986936!3d29.319182999999995!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1642017535643!5m2!1sen!2sin" width="1200" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe></p>
PISPHTML,
            ],
            [
                'slug' => 'facilities',
                'title' => 'Facilities',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Medical-test-prayaag-International.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Facilites : Life at prayaag</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Life At Prayaag</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Volly-Ball-court.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Swimming-coaching.webp" alt="" loading="lazy">

<p><span style="color: #000000;">Sports help to build character and educate the importance of discipline in life. It instills a respect for rules and allows the children to learn the value to self control. Keeping in mind, we strive to provide our students with one of the best indoor and outdoor sporting infrastructures in Panipat to prepare our children for the highly competitive world of sports.</span></p>
<p><span style="color: #000000;"><strong>We provide various sports facilities at PRAYAAG</strong></span></p>
<ul>
<li><span style="color: #000000;">Football and hockey ground with a 5-lane track.</span></li>
<li><span style="color: #000000;">International standard turf courts for basketball, badminton and tennis.</span></li>
<li><span style="color: #000000;">Swimming pool with a splash pool.</span></li>
<li><span style="color: #000000;">Gym for functional training of students.</span></li>
<li><span style="color: #000000;">Cricket, table tennis, yoga, chess, rock climbing and Karate.</span></li>
</ul>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Science Laboratories</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">Every student is an enthusiastic scientist in the making, and tries to explore, probe and experiment to find the truth behind the facts of life. To shape the world of tomorrow we have the best Science laboratories in Panipat for Physics, Chemistry and Biology that enable students to conduct all experiments prescribed by the CBSE syllabi. </span></p>
</div>
</div>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Physics Lab</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">We have a well planned and well equipped Physics lab with all the interesting sets of equipment to underpin scientific and experimental concepts and assist the children in developing investigative skills.</span></p>
</div>
</div>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Chemistry Lab</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">The Chemistry laboratory is planned while keeping all the statutory norms and safety standards. Here, a scientific approach is developed in the students along with the ability to analyze, collate, compute, integrate and deduce.</span></p>
</div>
</div>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Biology Lab</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">The Biology laboratory is a modern fact finding infrastructure which provides a broad range of biological and biochemical techniques with in-depth practical guidance offered by experienced staff.</span></p>
</div>
</div>
</div>
</div>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Math Lab</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">Maths lab is designed in a way where students can learn and explore various mathematics concepts and verify a range of mathematical facts and theorems using combination of activities. It is well equipped with necessary kits and tools. </span></p>
</div>
</div>
</div>
</div>

<div class="post-header">
<h2><span style="color: #000000;"><strong>Computer Lab</strong></span></h2>
<hr />
</div>
<div class="post-desc">
<div class="text">
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">We have a fully air conditioned, highly modernized computer lab with the latest technology and 24 hour internet access. The students are trained on various computer programs according to the demands of the modern times. </span></p>
</div>
</div>
</div>
</div>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/unesco-logo-260px.jpg" alt="" loading="lazy">

<div class="post-header">
<p><span style="color: #000000;"><strong>The basic work of this club is:</strong></span></p>
</div>
<div class="post-desc">
<div class="text">
<ul>
<li><span style="color: #000000;">Disseminate the general principles as those set out in the preamble and the constitution of UNESCO, the United Nations Charter and Universal declaration of Human Rights.</span></li>
<li><span style="color: #000000;">Participate in the celebration of International days and years proclaimed by the General Assembly of United Nations and General Conference of UNESCO.</span></li>
<li><span style="color: #000000;">Promote literacy activities, the preservation and presentation of the cultural heritage-Organize study camps for the students of foreign countries.</span></li>
<li><span style="color: #000000;">Educate children for the prevention of AIDS.</span></li>
</ul>
</div>
</div>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp" alt="" loading="lazy">

<div class="col-md-8 col-sm-12 col-xs-12">
<section class="blog-container">
<article class="blog-post style-two">
<div class="post-inner">
<div class="post-header">
<p><span style="color: #000000;">The school boasts of providing the two well-stocked separate best libraries for Juniors and Seniors in Panipat where all students feel welcome and encouraged to grow and learn from the range of books with an impressive index of titles, covering fiction and non-fiction, periodicals, magazines, and newspapers. Students are encouraged to make full use of these facilities in order to inculcate a love for books and the habit of reading from an early age. </span></p>
</div>
</div>
</article>
</section>
</div>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Class-prayaag-International-school.webp" alt="" loading="lazy">

<p><span style="color: #000000;"><strong>Prayaag International School</strong> is at par with international standards. Spacious classrooms equipped with latest infrastructure ensures that our students have the best resources to support the academic program. The school has fully centralized air-conditioned class rooms equipped with Smart Class (Digital Teaching System)   and a strength of 25-30 students for effective learning.</span></p>
<p><span style="color: #000000;"><strong>Teaching Methodologies </strong></span></p>
<ul>
<li><span style="color: #000000;">MCQ's and Worksheets</span></li>
<li><span style="color: #000000;">Virtual Laboratory of simulations</span></li>
<li><span style="color: #000000;">Mind maps</span></li>
<li><span style="color: #000000;">Teaching ideas and topic synopsis</span></li>
<li><span style="color: #000000;">Real life applications</span></li>
<li><span style="color: #000000;">Web links and diagram marker.</span></li>
</ul>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Transport-Buses-prayaag-international-school.webp" alt="" loading="lazy">

<div class="col-md-8 col-sm-12 col-xs-12">
<section class="blog-container">
<article class="blog-post style-two">
<div class="post-inner">
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">The need for safe passage of each and every child to school and back home is of utmost importance to us. To make sure safe travel, the school has its own best transport facility in Panipat which includes a fleet of outsourced school buses that are equipped with CCTVs and are designed as per standards and are manned by trained drivers. To supervise and monitor a transport attendant is on board throughout the journey.</span></p>
</div>
</div>
</div>
</article>
</section>
</div>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-Interational-medical-facility.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Security-Guard-Prayaag-International-e1642866822689.webp" alt="" loading="lazy">

<p><span style="color: #000000;">The safety and security of our students is our priority. The school has taken the initiative to install CCTV cameras IN AND around the school campus to ensure safety at all times.</span><br /><span style="color: #000000;">A separate play area is used by our younger students during the cooler seasons. Soft play areas and equipments provide a variety of activities and opportunities for exploration.</span><br /><span style="color: #000000;">We have well-equipped and spacious rooms providing a wide variety of resources that aim at stimulating the interest of young students during recreation as well as during class activities</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Transport-Buses-prayaag-international-school.webp" alt="" loading="lazy">

<div class="col-md-8 col-sm-12 col-xs-12">
<section class="blog-container">
<article class="blog-post style-two">
<div class="post-inner">
<div class="post-desc">
<div class="text">
<p><span style="color: #000000;">At PRAYAAG, we believe that tours and excursions are the perfect way to expand one's horizon. The students are persuaded to acquire knowledge and explore new things not just with in the boundaries but also beyond them. Every now and then International Educational Exchange Program is organized for the global exposure for the children.</span></p>
</div>
</div>
</div>
</article>
</section>
</div>
PISPHTML,
            ],
            [
                'slug' => 'disclosure',
                'title' => 'Disclosure',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-school-campus.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><strong>Disclosure</strong></h2>

</div></div>

<h2><span style="font-size: 150%; color: #f99b1c;">Disclosure</span></h2>
PISPHTML,
            ],
            [
                'slug' => 'book-list',
                'title' => 'Book List',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><strong>Book Lists</strong></h2>

</div></div>

<h4 style="text-align: center;"><span style="color: #f99b1c; font-size: 150%;">Book List 2023-24</span></h4>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/BOOK_LIST_PrayaagInternationalSchool.com_2023-24.pdf">Book List 2023-24</a>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Book-List-2019-20.pdf">Book List 2019-20</a>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Book-List-2020-21.pdf">Book List 2020-21</a>

<a class="imp-btn" href="#book">Book List 2021-22</a>

<h4 style="text-align: center;"><span style="color: #f99b1c; font-size: 150%;">Book List 2021-22</span></h4>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-Pre-Nur-III-20-21.pdf">Classes - Pre-Nursery - III</a>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-4-8.pdf">Booklist - Classes - IV - VIII</a>

<a class="imp-btn" href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-9-12.pdf">Booklist - Classes - IX - XII</a>
PISPHTML,
            ],
            [
                'slug' => 'career',
                'title' => 'Career',
                'seo_title' => 'Apply For Job | Career Opportunities at Prayaag International School, Panipat',
                'seo_desc' => 'Explore rewarding career opportunities at Prayaag International School in Panipat. Join a passionate and dedicated team committed to shaping the future of education. Discover your path to excellence with us.',
                'form' => true,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Career-at-Prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Career</strong></h3>

</div></div>

<h3 style="text-align: center;"><span style="color: #f99b1c; font-size: 150%;">Be a Part of the Team that Inspires the Next Generation.</span></h3>

<h2>Essential Qualifications</h2>
<ul>
<li style="text-align: left;"><strong>Qualification for Remaining Post:</strong> Minimum Post Graduation</li>
<li style="text-align: left;"><strong>Qualification for F.O.E. :</strong> Minimum Graduation in any stream with effective communication skills</li>
<li style="text-align: left;"><strong>Essential Experience for Remaining Post:</strong> Minimum 5 yrs.</li>
<li style="text-align: left;"><strong>Essential Experience for F.O.E. :</strong> Minimum 3 yrs.</li>
<li style="text-align: left;"><strong>Preferred Age:</strong> As per CBSE norms</li>
<li style="text-align: left;"><strong>Salary:</strong> Salary no constraints for deserving candidates.</li>
</ul>

<h4>*Interested candidates may send the resume at:</h4>
<p><a href="mailto:hr@pisp.in"><u>hr@pisp.in</u></a> </p>
<p><a title="Prayaag International School-homepage" href="https://prayaaginternationalschool.com/" target="_blank" rel="noopener">https://prayaaginternationalschool.com/</a></p>
<p>For any queries feel free to contact: – <a href="tel:0180-2565555">0180-2565555</a>, <a href="tel:0180-2575555">2575555</a>, <a href="tel:9350748851">+91 9350748851</a><br />Between 09:00 a.m – 03:00 p.m.; From Monday – Saturday.</p>
PISPHTML,
            ],
            [
                'slug' => 'media',
                'title' => 'Media',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Children-playing-at-swimimg-pool.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h2 class="uppercase"><span style="font-size: 120%;"><strong>Life at Prayaag</strong></span></h2>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Dance &amp; Music</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Dance_class.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/student-playing-keyboard.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Teacher-teaching-keyboard.webp" alt="" loading="lazy">

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Sports</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Football.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Shooting.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Basket.jpg" alt="" loading="lazy">

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Arts &amp; Craft</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Painting-practice-prayaag-student.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Painting-at-Prayaag-International-School.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-School-Laibrary.webp" alt="" loading="lazy">

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Fun Activities</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Junior-children-playing.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Children-playing-at-swimimg-pool.webp" alt="" loading="lazy">

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">News</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-08-21-at-10.50.47-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-09-30-at-10.16.22-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-10-08-at-2.28.58-PM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-10-09-at-9.41.27-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-10-18-at-8.45.26-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-11-10-at-2.24.58-PM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-11-11-at-4.53.30-PM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-11-16-at-10.00.53-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-08-21-at-10.50.47-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/News-5.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/WhatsApp-Image-2026-01-19-at-12.54.27-PM-1.jpeg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-09-30-at-10.16.21-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/WhatsApp-Image-2025-09-30-at-10.16.19-AM_1350x1350.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/News-6.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/News-4.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/News-2.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2025/12/News-1.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/WhatsApp-Image-2026-01-19-at-12.54.27-PM-1.jpeg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/news-123.jpeg" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'downloads',
                'title' => 'Downloads',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Download</strong></h3>

</div></div>
PISPHTML,
            ],
            [
                'slug' => 'admissions',
                'title' => 'Admissions',
                'seo_title' => 'Admissions Open for Session 2026-27 in Prayaag: Apply Now',
                'seo_desc' => 'Admissions open for 2026-27 at Prayaag International School, Panipat! From Pre-School to Secondary, unlock quality education. Apply now!',
                'form' => true,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Admin-Prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Best CBSE School in Panipat <br />Admissions Open 2026-27 </strong></h3>
<p class="uppercase"><strong>Give Your Child The Best Future at Prayaag International School Panipat.<br />
Limited Seats Available – Apply Now. </strong></p>
<a class="imp-btn" href="/registration">Online Registration</a>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Children-playing-at-swimimg-pool.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Painting-practice-prayaag-student.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2><span style="font-size: 150%; color: #f99b1c;">Admission Process</span></h2>

<p><span style="color: #000000;">Welcome to Prayaag International School, Panipat. Our admission process is meticulously designed to ensure a seamless and transparent journey for both parents and students. Here's a step-by-step guide to navigate through the process:</span></p>

<a class="imp-btn" href="/registration">Apply For Admission</a>

<h2>Creating Intellectual Environment for Students</h2>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-School-Morning-Assembly.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h3><span class="count-up">184</span> +</h3>

<p>Teacher &amp; Staff</p>

<h3><span class="count-up">96</span>&nbsp;+</h3>

<p>Events Held</p>

<h3><span class="count-up">1100</span> +</h3>

<p>Happy Parents</p>

<h3><span class="count-up">43</span> +</h3>

<p>Lab Projects</p>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">How To Apply?</span></h2>

<h3 style="text-align: left; color: #000;">Campus Tour</h3>
<p style="text-align: justify; color: #000;">We extend a warm invitation to both parents to explore our exquisite campus and indulge in the experience of our exceptional facilities. This visit serves to provide you with a deeper understanding of our school's mission and distinctive educational style.</p>
<h3 style="text-align: left; color: #000;">Interview</h3>
<p style="text-align: justify; color: #000;">Following the campus tour, we orchestrate a comprehensive half-hour interview with your family. This dialogue is an opportunity for you to pose inquiries and share insights about your family in a more personal and meaningful manner.</p>
<h3 style="text-align: left; color: #000;">Procuring Prospectus and Registration Form</h3>
<p style="text-align: justify; color: #000;">Procure the prospectus and registration form from our adept admission counselor, upon payment of the relevant <a href="https://prayaaginternationalschool.com/fee-structure/">fee</a>. The duly completed registration form, accompanied by attested photocopies of the stipulated documents, must be submitted to the admission office within the stipulated timeframe.</p>
<h3 style="text-align: left; color: #000;">Document Verification and Principal's Discussion</h3>
<p style="text-align: justify; color: #000;">Our diligent Admission Office will meticulously verify the submitted documents, followed by a significant meeting with the Principal to delve deeper into the admission process.</p>
<h3 style="text-align: left; color: #000;">Entrance Test (Class I onwards)</h3>
<p style="text-align: justify; color: #000;">For candidates aspiring for admission from Class I onward, an entrance test will be administered. Admissions will be based on merit and the candidate's performance in the entrance test.<br />NUR – I – One on One Interaction<br />II-IX – Written test of English, Math &amp; General Awareness<br />XII (All streams) – Aptitude Test<br />Documents Required for Admission:<br />Birth certificate<br />Proof of residence<br />School Leaving Certificate (for admission from Class II onwards)<br />Passport-sized photographs of the child and parents/guardians<br />Medical fitness certificate<br />Latest report card<br />Fee receipt<br />Original certificates for verification</p>
<h3 style="text-align: left; color: #000;">Admission Decisions: Tests and Interview</h3>
<p style="text-align: justify; color: #000;">Admissions are granted based on an admission test and a personal interaction involving both the student and their parents/guardians. This process unfolds at the onset of the academic session in April each year.</p>

<a class="imp-btn" href="https://wa.me/919350748851">Whatsapp Now</a>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Eligibility Criteria</span></h2>
<p style="text-align: center;"><span style="color: #000000; font-size: 90%;">Registration for admission to all classes starts from December.</span></p>

<p><span style="color: #000000;"><strong>For registration, duly filled in application form (attached with prospectus) is to be submitted along with:</strong></span></p>
<ul>
<li><span style="color: #000000;">4 Photographs of the student</span></li>
<li><span style="color: #000000;">2 Photographs of the parents</span></li>
<li><span style="color: #000000;">Original TC from previous School</span></li>
<li><span style="color: #000000;">Proof of Residence</span></li>
<li><span style="color: #000000;">Aadhar Card</span></li>
<li><span style="color: #000000;">Birth Certificate (issued by the civic body)</span></li>
</ul>

<p><span style="color: #000000;"><strong>Class I-VIII</strong></span></p>
<ul>
<li><span style="color: #000000;"><strong>NUR – I</strong> - One on One Interaction</span></li>
<li><span style="color: #000000;"><strong>II-IX</strong> - Written test of English, Math &amp; General Awareness</span></li>
<li><span style="color: #000000;">XII (All streams) - Aptitude Test</span></li>
</ul>

<p><span style="color: #000000;"><strong>The Age Criteria as on 1<sup>st</sup>&nbsp;April Will be as follows:</strong></span></p>
<ul>
<li><span style="color: #000000;"><strong>Pre-Nur&nbsp;</strong>&nbsp; ----- 2.5 Years</span></li>
<li><span style="color: #000000;"><strong>Nur&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ----- 3.5 Years</span></li>
<li><span style="font-size: 14.4px; color: #000000;"><strong>K.G.&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp; ----- 4.5 Years</span></li>
<li><span style="font-size: 14.4px; color: #000000;"><strong>Grade I&nbsp;&nbsp;&nbsp;</strong> ----- 6 Years</span></li>
</ul>

<h3>Note</h3>
<p style="text-align: justify; color:#000">The ultimate authority on admissions rests with the school's Principal. All decisions pertaining to admissions remain within the purview of the school's discretion.<br />
We are eagerly anticipating the prospect of welcoming you into the Prayaag International School fold. Our commitment lies in nurturing each child's latent potential, guiding them towards a trajectory of excellence. For further queries or assistance, please feel free to reach out to our dedicated admission office.</p>
PISPHTML,
            ],
            [
                'slug' => 'fee-structure',
                'title' => 'Fee Structure',
                'seo_title' => 'Fee Structure 2026-27 | Prayaag International School, Panipat.',
                'seo_desc' => 'Explore the fee structure for the academic year 2026-27 at Prayaag International School in Panipat. Find comprehensive details about tuition fees, admission charges, and other associated costs, ensuring transparency and informed decision-making for parents and students.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Fee-Structure.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Fee Structure</strong></h3>
<a class="imp-btn" href="/registration">Online Registration</a>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Fee Structure 2026-27</span></h2>

<p style="text-align: center;"><span style="font-size: x-large; color: #282828;"><strong>Grade Pre Nursery - I </strong></span></p>

<p style="text-align: center;"><span style="color: #000000;">Tuition Fee Per Month</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">7250</span></p>

<p style="text-align: center;"><span style="color: #000000;">Total Annual Fee</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">87000</span></p>

<p style="text-align: center;"><span style="font-size: x-large; color: #282828;"><strong>Grade II-V</strong></span></p>

<p style="text-align: center;"><span style="color: #000000;">Tuition Fee Per Month</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">7750</span></p>

<p style="text-align: center;"><span style="color: #000000;">Total Annual Fee</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">93000</span></p>

<p style="text-align: center;"><span style="font-size: x-large; color: #282828;"><strong>Grade VI-VIII</strong></span></p>

<p style="text-align: center;"><span style="color: #000000;">Tuition Fee Per Month</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">8000</span></p>

<p style="text-align: center;"><span style="color: #000000;">Total Annual Fee</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">96000</span></p>

<p style="text-align: center;"><span style="font-size: x-large; color: #282828;"><strong>Grade IX-X</strong></span></p>

<p style="text-align: center;"><span style="color: #000000;">Tuition Fee Per Month</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">8500</span></p>

<p style="text-align: center;"><span style="color: #000000;">Total Annual Fee</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">102000</span></p>

<p style="text-align: center;"><span style="font-size: x-large; color: #282828;"><strong>Grade XI-XII</strong></span></p>

<p style="text-align: center;"><span style="color: #000000;">Tuition Fee Per Month</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">8750</span></p>

<p style="text-align: center;"><span style="color: #000000;">Total Annual Fee</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">105000</span></p>

<p style="text-align: center;"><span style="color: #000000;">Registration</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">One Time (At the time of Admission)<br />1000</span></p>

<p style="text-align: center;"><span style="color: #000000;">Security</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">Refundable<br />10000</span></p>

<p style="text-align: center;"><span style="color: #000000;">Admission Charges</span></p>
<hr>
<p style="text-align: center;"><span style="color: #000000;">One Time<br />20000</span></p>

<h3>Note:</h3>
<p style="text-align: justify; color: #000;">The above fee structure is inclusive of all academic facilities and does not cover additional activities or special programs.<br />Transportation, books and uniform have separate charges.<br />The Security fee is refundable at the end of the student's tenure at the school.<br />All fees are payable in advance, on a quarterly, bi-annual, or annual basis, as per the school's policy.</p>
<p style="text-align: justify; color: #000;">We believe that quality education is an investment in the future. Prayaag International School, Panipat is committed to providing an enriching learning experience for your child. For any clarification or further assistance regarding the fee structure or any other aspect of the school, please feel free to reach out to our administration. We are here to support you in every step of your child's educational journey.</p>
PISPHTML,
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'seo_title' => 'About PISP | Best CBSE Schools in Panipat | Top 5 Schools Panipat',
                'seo_desc' => 'Discover PISP, the best schools in Panipat committed to providing top-quality education and nurturing young minds for a bright future.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<p>Our School Story</p>
<h2 class="uppercase"><strong>About us</strong></h2>

</div></div>

<p style="color: #000000;">Prayaag International School, Panipat located in the heart of the city, is more than just an educational institution; it's a nurturing ground for young minds, a place where aspirations are nurtured and potential is shaped. Since its inception in 2016, the school has been dedicated to providing holistic education that goes beyond textbooks, fostering a stimulating environment where students can excel academically, emotionally, and socially.</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Self-defence-practice-at-prayaag-international-school.webp" alt="" loading="lazy">

<h3><span style="color: #f99b1c;">Our Vision</span></h3>

<p><span style="color: #000000;">Prayaag International School has as its vision and mission "Character Building and Man-Making" and its motto is "Discipline and Excellence". Its belief is "Co-operation over Competition".</span></p>
<p><span style="color: #000000;">The goals of <a href="https://prayaaginternationalschool.com/"><strong>PRAYAAG INTERNATIONAL SCHOOL, PANIPAT</strong></a> are defined by the verses of the Sthithaprajna from the <strong>Bhagvad Gita</strong> – namely true wisdom that transcends all text books.</span></p>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Message from Our Principal </span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/principal-prayaag-International-school.webp" alt="" loading="lazy">

<p><span style="color: #000000;">The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</span></p>
<p><span style="color: #000000;">We believe that –&nbsp;<strong>IF A CHILD CANNOT LEARN THE WAY WE TEACH, TEACH HIM THE WAY HE CAN LEARN</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is&nbsp;encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</span></p>

<h3><span style="color: #f99b1c;">Our Mission</span></h3>

<p><span style="color: #000000;">Our Mission is to provide a comprehensive and future-oriented education that empowers students to become lifelong learners, confident decision-makers, and compassionate individuals. We aim to foster an atmosphere of inclusivity and collaboration, where every student's unique talents are recognized and nurtured.</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/student-playing-football-1.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Yoga-at-Prayaag-International-School.webp" alt="" loading="lazy">

<h3><span style="color: #f99b1c;">Our Values</span></h3>

<p><span style="color: #000000;">“Pursuing Excellence and Embrace Responsibility”</span><br /><span style="color: #000000;">We raise intellectual standard of our children by promoting a school ethos </span><span style="color: #000000;">that is underpinned by the core value - growing by learning.&nbsp;&nbsp;&nbsp;</span></p>

<h6><span style="color: #000000;"</span></h6>

<h2><span style="font-size: 150%; color: #f99b1c;">Why Prayaag?</span></h2>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">Awarded the prestigious British Council International School Award, 2020-23</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">100% results in academics, sports and co-scholastic events.</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">Highly educated and experienced teachers that imbibe a contemporary, all-around education to make today’s students the leaders of tomorrow.</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">Superior indoor and outdoor sports infrastructure enhanced by highly-experienced trainers to ensure physical development and nurture the sportsperson inside every student.</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">World-class infrastructure equipped with state-of-the-art digital facilities to impart a holistic education to all the students.</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">Special periodic sessions to foster personal well-being, ethics and personality development skills in all the students and teachers alike.</span></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/check-line.png" alt="" style="width:44px;height:auto;display:inline-block">

<p><span style="color: #000000;">GPS and security cameras equipped, fully-air-conditioned infrastructure and transport buses to ensure safety and comfort.</span></p>

</div>

<h2><span style="color: #f99b1c; font-size: 150%;">Our Achievements</span></h2>

<p style="text-align: center;"><span style="color: #000000;">Discover excellence in education at Prayaag International School, Panipat – renowned among the <a href="https://prayaaginternationalschool.com/">best CBSE schools in Panipat</a>. Our students consistently excel in various academic competitions, sports events, and cultural activities. These accolades are a testament to the dedication of our students, faculty, and staff in creating a nurturing and thriving learning environment. Every day is a stepping stone in this journey but here are some of the finest steps that we took.</span></p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/compass.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3>2016</h3>

<p><span style="font-weight: 400; color: #000000;">2016-Laid the foundation stone of the school</span></p>

<h3>2019</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">District Level Karate Competition&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Wrestling Competition (Block level)</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Capacity Building Programme By CBSE&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">Annual Function- Let Me Fly</span> </span></li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/canvas.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/paper.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3>2020</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Vidya Mandir Quest- Biggest National Level Quiz&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">British Council International School Award&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Go Green Initiative</span></li>
</ul>

<h3>2021</h3>

<ul>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">National Level Karate Championship</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">&nbsp;Building Resilience- A virtue in Covid Times&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Dhammika KAT Cup Championship&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Sports Tournament- District Level&nbsp;</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400; color: #000000;">Faculty/ Staff Sports Tournament (Why should students have all the fun?)</span></li>
<li style="font-weight: 400;" aria-level="3" aria-checked="false"><span style="font-weight: 400;"><span style="color: #000000;">State Level Painting Competition</span> </span></li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/02/pallete.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h2><span style="color: #f99b1c; font-size: 150%;">Our Governing Body</span></h2>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>
PISPHTML,
            ],
            [
                'slug' => 'alumni',
                'title' => 'Alumni',
                'seo_title' => 'Prayaag International School, Panipat Alumni | Shaping Futures, Inspiring Excellence',
                'seo_desc' => 'Explore the accomplished alumni of Prayaag International School in Panipat. Discover their inspiring stories of success and how they continue to shape the world around them.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Alumni</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Alumni</span></h2>

<h2 style="color: #000000;text-align: center;">Welcome to the Prayaag International School, Panipat Alumni Page</h2>
<p style="color: #000000;text-align: justify;">At Prayaag International School, we take immense pride in our alumni community – a network of individuals who have not only excelled in their chosen fields but have also carried the values and ethos of our institution to every corner of the world. This page is a dedicated space to celebrate your achievements, reconnect with old friends, and continue being a part of the vibrant Prayaag family.</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Alumni.jpg" alt="" loading="lazy">

<h3 style="color: #000000;text-align: center;">Stay Connected</h3>
<p style="color: #000000; text-align: justify;">We believe that the bond between the school and its alumni is everlasting. Stay connected with us to keep up with the latest happenings, events, and developments at Prayaag International School. Update your contact information and follow us on social media to receive updates about reunions, workshops, and other exciting opportunities to reconnect.</p>

<h3 style="color: #000000;text-align: center;">Share Your Journey</h3>
<p style="color: #000000; text-align: justify;">Your journey since leaving Prayaag International School is a story worth sharing. We invite you to share your experiences, accomplishments, and milestones with us. Whether it's a groundbreaking project, a new business venture, or a personal achievement, your story can inspire the current students and fellow alumni.</p>

<div class="imp-feature">

<h3 style="color: #000000;text-align: center;">Giving Back</h3>
<p style="color: #000000; text-align: justify;">As alumni, you are an integral part of our school's legacy. Your support can make a difference in the lives of current students. Whether through scholarships, guest lectures, or mentoring programs, your contribution can shape the next generation of leaders and thinkers.</p>

</div>

<p>For any queries, suggestions, or to share your updates, please contact our Alumni Relations team at alumni@pisp.in</p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/contact-us/">Mail Us</a>

<h4 class="uppercase">Reunions and Events</h4>
<p style="color: #000000; text-align: justify;">Reunions are a perfect opportunity to relive the memories, create new ones, and reconnect with classmates and teachers. Keep an eye on this section for updates about upcoming reunions and events. Don't miss the chance to come back to where it all began.</p>

<p style="color: #000000; text-align: justify;">To ensure you don't miss out on any updates, please keep your contact information updated. Let us know about your achievements and milestones so we can celebrate them together. You can also share your feedback and suggestions to help us improve the alumni experience.</p>

<p style="color: #000000; text-align: justify;">Thank you for being an integral part of the Prayaag International School family. Your journey is an inspiration to us all, and we look forward to celebrating your continued success. Stay connected, stay engaged, and keep the Prayaag spirit alive!</p>
PISPHTML,
            ],
            [
                'slug' => 'classrooms',
                'title' => 'Classrooms',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-International-Junior-Classroom.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Classrooms</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Classrooms</span></h2>

<p style="text-align:justify;"><span style="color: #000;"><strong>Prayaag International School, Panipat</strong> has spacious classrooms equipped with latest infrastructure which ensure that our students have the best resources to support the academic program. The school has centralized air-conditioned class rooms equipped with Smart Class (Digital Teaching System)   and a strength of 25-30 students for effective learning.</span></p>
<p><span style="color: #000;"><strong>Teaching Methodologies </strong></span></p>
<ul style="text-align:justify;">
<li><span style="color: #000;">MCQ's and Worksheets</span></li>
<li><span style="color: #000;">Virtual Laboratory of simulations</span></li>
<li><span style="color: #000;">Mind maps</span></li>
<li><span style="color: #000;">Teaching ideas and topic synopsis</span></li>
<li><span style="color: #000;">Real life applications</span></li>
<li><span style="color: #000;"></span></li>
</ul>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Classrooms.jpg" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'labs',
                'title' => 'Labs',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Labs-Prayaag-International-School.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Our Labs</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Labs</span></h2>

<h2 style="color: #000000;">Science Laboratories</h2>
<p style="color: #000000;text-align:justify;">Every student is an enthusiastic scientist in the making, and tries to explore, probe and experiment to find the truth behind the facts of life. To shape the world of tomorrow we have the best Science laboratories in Panipat for Physics, Chemistry and Biology that enable students to conduct all experiments prescribed by the CBSE. </p>

<h2 style="color: #000000;">Physics Lab</h2>
<p style="color: #000000;text-align:justify;">We have a well planned and well equipped Physics lab with all the interesting sets of equipment to underpin scientific and experimental concepts and assist the children in developing investigative skills.</p>

<h2 style="color: #000000;">Chemistry Lab</h2>
<p style="color: #000000;text-align:justify;">The Chemistry laboratory is planned while keeping all the statutory norms and safety standards. Here, a scientific approach is developed in the students along with the ability to analyze, collate, compute, integrate and deduce.</p>

<h2 style="color: #000000;">Biology Lab</h2>
<p style="color: #000000;text-align:justify;">The Biology laboratory is a modern fact finding infrastructure which provides a broad range of biological and biochemical techniques with in-depth practical guidance offered by experienced staff.</p>

<h2 style="color: #000000;">Math Lab</h2>
<p style="color: #000000;text-align:justify;">Maths lab is designed in a way where students can learn and explore various mathematics concepts and verify a range of mathematical facts and theorems using combination of activities. It is well equipped with necessary kits and tools. </p>

<h2 style="color: #000000;">Computer Lab</h2>
<p style="color: #000000;text-align:justify;">We have a fully air conditioned, highly modernized computer lab with the latest technology and 24 hour internet access. The students are trained on various computer programs according to the demands of the modern times. </p>

<h2 style="color: #000000;">Robotics Lab</h2>
<p style="color: #000000;text-align:justify;">At Prayaag International School, we are committed to providing our students with the skills and knowledge they need to excel in today's rapidly evolving world of technology. Our Robotics School Lab serves as a dynamic hub for innovation, fostering creativity and hands-on learning that empowers students to delve into the captivating realm of robotics and automation.</p>
PISPHTML,
            ],
            [
                'slug' => 'library',
                'title' => 'Library',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Lib_new_1.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Library</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Library</span></h2>

<p style="color: #000000;text-align:justify;">The school boasts of two well-stocked libraries, separately for Juniors and Seniors where all students feel welcome and encouraged to grow and learn from the range of books with an impressive index of titles, covering fiction and non-fiction, periodicals, magazines, and newspapers. Students are encouraged to make full use of these facilities in order to inculcate a love for books and the habit of reading from an early age. </p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'sports',
                'title' => 'Sports',
                'seo_title' => 'Sports Facilities at Prayaag International School, Panipat',
                'seo_desc' => 'Explore world-class sports facilities at Prayaag International School, Panipat. Encouraging fitness, teamwork, discipline, and all-round student development.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/sports_prayaag_international_panipat.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>SPORTS</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Sports</span></h2>

<p><span style="color: #000000;">Sports help to build character and educate the importance of discipline in life. It instills respect for rules and allows children to learn the value of self control. Keeping in mind, we strive to provide our students with one of the best indoor and outdoor sporting infrastructures in Panipat to prepare our children for the highly competitive world of sports.</span></p>
<p><span style="color: #000000;"><strong>We provide various sports facilities at PRAYAAG</strong></span></p>
<ul>
<li><span style="color: #000000;">Shooting Range, Football and Hockey ground with a 5-lane track.</span></li>
<li><span style="color: #000000;">International standard turf courts for Volleyball, Basketball, Badminton and Lawn Tennis.</span></li>
<li><span style="color: #000000;">Swimming Pool with a Splash Pool.</span></li>
<li><span style="color: #000000;">Gym for functional training of students.</span></li>
<li><span style="color: #000000;">Cricket, Table Tennis, Yoga, Chess and Karate.</span></li>
</ul>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Shooting.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Basket.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/lawn_tennis_prayaag_international_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/cricket_prayaag_international_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/table_tennis_prayaag_international_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2026/02/Badminton.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/swimming-sport1_prayaag_international_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/skating_prayaag_international_panipat-1.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/volleyball_prayaag_international_panipat.jpg" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'transportations',
                'title' => 'Transportations',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Transport-Buses-prayaag-international-school.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>transportation</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Transportation</span></h2>

<p style="text-align:justify;"><span style="color: #000000;">The need for safe passage of each and every child to school and back home is of utmost importance to us. To ensure safe travel, the school has its own transport facility  which includes a fleet of Air Conditioned school buses that are equipped with CCTVs and are designed as per standards and are manned by trained drivers. To supervise and monitor a Transport Attendant is on board throughout the journey.</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Transport-Buses-prayaag-international-school.webp" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'safety-security',
                'title' => 'Safety & Security',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Medical-test-prayaag-International.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>safety and security</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Safety & Security</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Prayaag-Interational-medical-facility.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/security_prayaag_international_panipat.jpg" alt="" loading="lazy">

<p style="text-align:justify;"><span style="color: #000000;">The school has created a safe and secure environment, ensuring the well-being of everyone on campus. Fences and secure gates are in place around the school perimeter to control access. Entry points to school are monitored and controlled.<br />
CCTV Cameras are installed in key areas such as entrances, Classrooms, Amphitheatre, Swimming Pool, common areas, and parking lots. Intercom and PA Systems & Emergency Notification Systems are in place. Trained security personnel (Male and Female) have been employed to patrol the campus and respond to incidents.</span></p>
PISPHTML,
            ],
            [
                'slug' => 'tours-and-excursions',
                'title' => 'Tours and Excursions',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/09/School-Trip-Banner-Prayaag-International-School-Panipat.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Tours and Excursions</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Tours and Excursions</span></h2>

<p style="text-align:justify;"><span style="color: #000000;">At PRAYAAG, we believe that tours and excursions are the perfect way to expand one's horizon. The students are persuaded to acquire knowledge and explore new things not just with in the boundaries but also beyond them. Every now and then International Educational Exchange Program is organized for the global exposure for the children.</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/school_trip_prayaag_school_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/school_trip_prayaag_school_panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/School-Trip3-Prayaag-International-School-Panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Transport-Buses-prayaag-international-school.webp" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/School-Trip-Prayaag-International-School-Panipat.jpg" alt="" loading="lazy">

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/School-Trip2-Prayaag-International-School-Panipat.jpg" alt="" loading="lazy">
PISPHTML,
            ],
            [
                'slug' => 'unesco',
                'title' => 'UNESCO',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/09/Unesco_Img.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">UNESCO</span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/Hands.jpg" alt="" loading="lazy">

<div class="post-header">
<p><strong>The basic work of this club is:</strong></p>
</div>
<div class="post-desc">
<div class="text">
<ul>
<li>Disseminate the general principles as those set out in the preamble and the constitution of UNESCO, the United Nations Charter and Universal declaration of Human Rights.</li>
<li>Participate in the celebration of International days and years proclaimed by the General Assembly of United Nations and General Conference of UNESCO.</li>
<li>Promote literacy activities, the preservation and presentation of the cultural heritage-Organize study camps for the students of foreign countries.</li>
<li>Educate children for the prevention of AIDS.</li>
</ul>
</div>
</div>
PISPHTML,
            ],
            [
                'slug' => 'top-10-schools-in-panipat',
                'title' => 'Top 10 Schools in Panipat',
                'seo_title' => 'Top 10 Schools in Panipat for Quality Education 2025-26',
                'seo_desc' => 'List of top 10 schools in Panipat Unlock a World of Quality Education for Your Child PISP proudly listed among the top 10 schools in Panipat.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Fee-Structure.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h1 class="uppercase" style="font-size:50px;"><strong>Top 10 Schools in panipat</strong></h1>

</div></div>

<h1><span style="color: #f99b1c; font-size: 22px;"><a href="https://prayaaginternationalschool.com/">Prayaag International School</a> is a Standout Institution Among Panipat's Top Ten Schools.</span></h1>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/04/TOP-10-SCHOOLS-IN-PANIPAT.jpg" alt="" loading="lazy">

<p style="color: #000000; text-align: justify;">At Prayaag International School, the educational philosophy transcends traditional boundaries. Its curriculum is meticulously crafted not just to meet but exceed the stringent standards set by educational boards, ensuring a comprehensive learning experience. The school's approach is rooted in the belief that education is a journey shaping character, instilling values, and nurturing creativity.</p>
<p style="color: #000000; text-align: justify;">What sets Prayaag International School apart is its dedication to creating a dynamic and stimulating environment beyond the classroom. The faculty, comprised of seasoned educators and experts, passionately fosters a love for learning. Through interactive teaching methodologies, students are encouraged to think critically, question actively, and engage deeply with subjects.</p>
<p style="color: #000000; text-align: justify;">The school's infrastructure reflects its commitment to a conducive learning atmosphere. State-of-the-art classrooms, well-equipped laboratories, a rich library, and modern sports facilities contribute to a holistic educational experience. Prayaag International School understands that a well-rounded education includes not only academic pursuits but also physical fitness, artistic expression, and personality development.</p>
<p style="color: #000000; text-align: justify;">At the heart of the institution's success is its emphasis on character building and values. The school's ethos revolves around instilling qualities of integrity, compassion, and social responsibility in students. Beyond academics, Prayaag International School encourages participation in extracurricular activities, ensuring the development of essential life skills such as leadership, teamwork, and resilience.</p>
<p style="color: #000000; text-align: justify;">Prayaag International School's commitment to quality education is further exemplified by its engagement with the latest educational technologies. The school recognizes the importance of staying abreast of advancements and seamlessly integrating them into the curriculum. This forward-thinking approach prepares students for the challenges of the future, equipping them with the skills and knowledge needed to thrive in a rapidly evolving world.</p>
<p style="color: #000000; text-align: justify;">The school's recognition among the <a href="https://prayaaginternationalschool.com/top-10-schools-in-panipat/">Top 10 Schools in Panipat</a> is a testament to the success of its Students. Prayaag International School alumni are not only academically proficient but also confident individuals contributing meaningfully to society. The institution takes pride in nurturing future leaders, thinkers, and innovators prepared to make a positive impact on the global stage.</p>
<p style="color: #000000; text-align: justify;">Prayaag International School stands tall among the <a href="https://prayaaginternationalschool.com/">Top 10 Schools in Panipat</a>, distinguished by its unwavering commitment to providing quality education. With a holistic approach, state-of-the-art facilities, a dedicated faculty, and a focus on values, the school creates an environment where students thrive academically, emotionally, and socially. Choosing Prayaag International School is not just a selection of an educational institution but a commitment to nurturing a well-rounded, accomplished, and ethical individual ready to face the world with confidence and competence.</p>
<p><strong><a href="https://prayaaginternationalschool.com/best-schools-in-samalkha/">Best Schools in Samalkha</a></strong></p>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Message from Our Principal </span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/principal-prayaag-International-school.webp" alt="" loading="lazy">

<p><span style="color: #000000;">The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</span></p>
<p><span style="color: #000000;">We believe that –&nbsp;<strong>IF A CHILD CANNOT LEARN THE WAY WE TEACH, TEACH HIM THE WAY HE CAN LEARN</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is&nbsp;encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</span></p>

<h2><span style="color: #f99b1c; font-size: 150%;">Our Governing Body</span></h2>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>
PISPHTML,
            ],
            [
                'slug' => 'best-schools-in-samalkha',
                'title' => 'Best Schools in Samalkha',
                'seo_title' => 'Best Schools in Samalkha for Quality Education 2025-26',
                'seo_desc' => 'Are you trying to find the best Schools in Samalkha? Prayaag International School provides your child with an excellent education and a caring atmosphere.',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/04/best-schools-in-samalkha.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h1 class="uppercase" style="font-size:50px;"><strong>Best School in Samalkha</strong></h1>

</div></div>

<h1><span style="color: #f99b1c; font-size: 22px;"><a href="https://prayaaginternationalschool.com/">Prayaag International School,</a> A Beacon of Excellence Among the Best Schools in Samalkha</span></h1>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/04/Best-CBSE-School-in-Samalkha-1.jpg" alt="" loading="lazy">

<p>Prayaag International School: Preserving Quality as the <a href="https://prayaaginternationalschool.com/best-schools-in-samalkha/">Best School In Samalkha</a>, Prayaag International School is the epitome of academic brilliance, representing a dedication to developing students' whole selves. As the greatest school in Samalkha, we aim to provide a transformative learning environment that gives kids the tools they need to succeed intellectually, emotionally, and socially. We go beyond conventional educational paradigms. <br />The foundation of our educational philosophy is the conviction that learning is a journey that takes place outside of the classroom. We take pride in going above and beyond the strict requirements imposed by school boards, making sure that our curriculum is painstakingly designed to offer a thorough education that equips kids for the challenges of the future.</p>
<p>Prayaag International School is unique because of our steadfast commitment to fostering an environment that is vibrant and engaging outside of the classroom. Our faculty, which is made up of seasoned instructors and subject matter experts, is incredibly passionate about encouraging students to embrace studying. By utilizing interactive teaching approaches, we foster critical thinking, active questioning, and deep engagement with the subjects that students study. <br />Our infrastructure demonstrates our dedication to offering a favorable environment for learning. Our cutting-edge learning environments, well-stocked labs, extensive library, and cutting-edge sports facilities all support students' intellectual, physical, and emotional development.</p>
<p>Prayaag International School's success can be attributed to its focus on character development and values. Our mission is to prepare our students to be morally upright and responsible global citizens by teaching them values such as integrity, compassion, and social responsibility. In addition to academics, we promote extracurricular involvement among students to guarantee the development of critical life skills like resilience, leadership, and teamwork. <br />Our use of the newest instructional tools is just one more way that we demonstrate our dedication to providing high-quality education. We understand how critical it is to keep up with technological developments and incorporate them into our curriculum in a smooth manner. This progressive approach guarantees that our students have the information and abilities necessary to prosper in a world that is changing quickly.</p>
<p><a href="https://prayaaginternationalschool.com/">Prayaag International School</a>, regarded as one of Samalkha's Best Schools, is proud of its students' achievements. In addition to their academic prowess, our alumni are self-assured people who significantly impact society. We take great satisfaction in raising up the next generation of innovators, leaders, and thinkers who are ready to contribute to society on a worldwide scale. <br />Selecting Prayaag International School is a commitment to developing a well-rounded, successful, and moral person as much as a choice of educational institution. We cordially invite you to accompany us on this voyage of excellence and exploration, where each student is equipped to realize their greatest potential and turn into a lighthouse for the globe.</p>
<p><strong><a href="https://prayaaginternationalschool.com/admissions/">For the Admission Process Details, Click Here</a></strong></p>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Message from Our Principal </span></h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/principal-prayaag-International-school.webp" alt="" loading="lazy">

<p><span style="color: #000000;">The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</span></p>
<p><span style="color: #000000;">We believe that –&nbsp;<strong>IF A CHILD CANNOT LEARN THE WAY WE TEACH, TEACH HIM THE WAY HE CAN LEARN</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is&nbsp;encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</span></p>

<h2><span style="color: #f99b1c; font-size: 150%;">Our Governing Body</span></h2>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>

<h4>A title</h4>
<p>Image Box text</p>
PISPHTML,
            ],
            [
                'slug' => 'junior',
                'title' => 'Junior',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => true,
                'html' => <<<'PISPHTML'
<h1 style="font-family: 'Book Antiqua', Palatino, serif;">Welcoming Our Children,<br />Back To Their Favorite Preschool.<br />
    </h1>

<p style="font-family: 'Montserrat'; font-weight: 200;">We all want to select the best preschool for our kid's primitive educational years and build a strong foundation for the child's future.</p>
<p style="font-family: 'Montserrat'; font-weight: 200;">Little Millennium Preschool is one of the best preschools in India to lay the first step into the educational journey of your child with:</p>

<h1 style="font-family: 'Book Antiqua', Palatino, serif;">Preschool Admission Form</h1>

<p style="font-family: 'Montserrat'; font-weight: 500;">Fill in the form below, and we will get in touch with you to resolve the preschool admission queries at the earliest.</p>

<h2 style="font-family: 'Book Antiqua', Palatino, serif;">Admission Process at Little Millennium</p>

<p style="font-family: 'Montserrat'; font-weight: 200;">The Admission process at Little Millennium is simple and straightforward</P></p>

<h2>1</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Our admission counsellors will get in touch to understand the requirement</p>

<h2>2</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Our admission counsellors will get in touch to understand the requirement</p>

<h2>3</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Our admission counsellors will get in touch to understand the requirement</p>

<h2>4</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Our admission counsellors will get in touch to understand the requirement</p>

<h2 style="font-family: 'Book Antiqua', Palatino, serif;">Program Levels at Little Millennium</h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/10/Photo_Gallery.jpg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In est sem, ultrices ornare molestie sit amet, placerat vel arcu. Phasellus quis massa id sem pretium dictum. Donec sed sollicitudin est, sit amet eleifend ipsum. Vivamus nec pretium turpis."</p>

<h4 style="margin-bottom: 0px;">Kirstin W. Everton</h4>
<p><span style="font-size: 80%;">Graphic Designer, Apple</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/sports_school_prayaag_int_panipat.jpg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In est sem, ultrices ornare molestie sit amet, placerat vel arcu. Phasellus quis massa id sem pretium dictum. Donec sed sollicitudin est, sit amet eleifend ipsum. Vivamus nec pretium turpis."</p>

<h4 style="margin-bottom: 0px;">Kimberly Mason</h4>
<p><span style="font-size: 80%;">Customer Service, Google</span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/10/Events.jpg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In est sem, ultrices ornare molestie sit amet, placerat vel arcu. Phasellus quis massa id sem pretium dictum. Donec sed sollicitudin est, sit amet eleifend ipsum. Vivamus nec pretium turpis."</p>

<h4 style="margin-bottom: 0px;">Kimberly Mason</h4>
<p><span style="font-size: 80%;">Customer Service, Google</span></p>

<h2  style="font-family: 'Book Antiqua', Palatino, serif;">Importance Of Preschool<br />
</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Preschool Helps Young Children To Prepare, Learn, Grow.</p>

<a class="imp-btn" href="#">read more!</a>

<h2  style="font-family: 'Book Antiqua', Palatino, serif;">Choosing The Right Preschool<br />
</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Preschool Helps Young Children To Prepare, Learn, Grow.</p>

<a class="imp-btn" href="#">read more!</a>

<h2  style="font-family: 'Book Antiqua', Palatino, serif;">Frequently Asked Questions<br />
</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Preschool Helps Young Children To Prepare, Learn, Grow.</p>

<a class="imp-btn" href="#">read more!</a>

<h2  style="font-family: 'Book Antiqua', Palatino, serif;">Centre<br />
Locator<br />
</h2>

<p style="font-family: 'Montserrat'; font-weight: 400;">Preschool Helps Young Children To Prepare, Learn, Grow.</p>

<a class="imp-btn" href="#">read more!</a>

<h1 style="font-family: 'Book Antiqua', Palatino, serif;">What Parents Say</p>
</h2>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Asset25.png');background-size:cover;background-position:center"><div class="imp-hero-in">

<p style="font-family: 'Montserrat'; font-weight: 400;">The online classes conducted are engaging. My child looks forward to attending the sessions every day. With Little Millennium, I am confident that my child is getting the best early childhood grooming.</p>

</div></div>
PISPHTML,
            ],
            [
                'slug' => 'junior-landing-page',
                'title' => 'Junior Landing Page',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => true,
                'html' => <<<'PISPHTML'
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQVDJDH7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/cropped-prayaag-school-logo.png" alt="" loading="lazy">

<h1 style="font-family: Papyrus; letter-spacing: 6px;">WELCOMING OUR LITTLE LEARNERS,<br />TO YOUR FAVORITE PRE/PRIMARY SCHOOL!</h1>

<p style="font-family: 'Helvetica'; font-weight:500;">As you begin your child's educational journey, choosing the right school is important, and that is why we at Prayaag International School's Junior Wing welcome you. Our team comprised of highly skilled and knowledgeable teachers is committed to building a strong foundation for the future of your kid's success.</p>

<h1 style="font-family: Papyrus">Junior Wing  Admission Form</h1>

<p style="font-family: 'Helvetica'; font-weight:500;">Fill in the form below, and we will get in touch with you to resolve the pre/primary school admission queries at the earliest.</p>

<h2 style="font-family: Papyrus; margin-top: 90px; ">ADMISSION PROCESS<br /> AT PRAYAAG INTERNATIONAL SCHOOL, PANIPAT</h2>

<p style="font-family: 'Helvetica'; font-weight:550;">We at Prayaag, believe that initial days at school bring the best out of young learners.<br />
It's as simple as ABC to enroll for Prayaag International School, Panipat. </p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/compass.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3 style="font-family: Papyrus; ">Step 1</h3>

<p style="font-family: 'Helvetica'; font-weight:500;">Fill in the form below, and we will get in touch with you to resolve the admission queries at the earliest. </p>
<li 

<p style="font-family: 'Helvetica'; font-weight:500;">
NUR – I – One on One Interaction  </p>
<li 

<p style="font-family: 'Helvetica'; font-weight:500;">
II – V – Admission Test and Interaction</p>

<h3 style="font-family: Papyrus; ">Step 2</h3>

<p s

<p style="font-family: 'Helvetica'; font-weight:500;">Our Admission Counsellors will help you with the entire process.</p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/canvas.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/paper.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h3 style="font-family: Papyrus; ">Step 3</h3>

<p

<p style="font-family: 'Helvetica'; font-weight:500;">Visit Prayaag International School, Panipat for a guided tour.</p>

<h3 style="font-family: Papyrus; ">Step 4</h3>

<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Enrolment process completion with assistance from Admission Counsellors.</p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/02/pallete.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>

<h2 style="font-family: Papyrus;">Curriculum And Programs</p>

<p s

<p style="font-family: 'Helvetica'; font-weight:500;">Join us in shaping the leaders of tomorrow.</p>

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 1: From Tiny Sprout to Vibrant Blossom (3 yrs - 4 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">You can watch your kid grow into a self-assured learner! The focus is on creating a balanced program that nurtures the whole child, preparing them for the next steps in their educational journey. We support their development, help them get over their fears, and kindle their curiosity. We offer concentrated teaching on phonics and numbers. Promoting fine motor skills like running, jumping and kicking through activities. Expanding vocabulary and improving communication skills through conversation, story telling and rhymes. Encouraging interaction with peers to develop social skills such as sharing, cooperation, and empathy.  </p>

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 2: Seedlings of Knowledge (4 yrs -  5 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Between the ages of 4 to 5 years, our children learn a variety of skills and knowledge that form the foundation for their future education. Recognizing and writing simple words and beginning to read very short sentences. Learning basic Arithmetic concepts, shapes, sizes and patterns. Learning to share, take turns, and work in groups. Developing Fine Motor Skills like drawing, coloring, tearing and pasting to develop hand-eye coordination, running, jumping, climbing, and other physical activities to build strength and develop balancing. Developing Language of the children by engaging them in conversations, storytelling, and following basic instructions.</p>

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 3: FLOWER BLOOMING IN BREEZE II (5 yrs -  6 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Mastering Fundamental Skills in reading, writing, and mathematics to build a strong academic foundation.</p>
<p>Enhancing verbal and non-verbal communication, fostering the ability to express thoughts and feelings effectively. Cultivating Social Skills to interact positively with peers and adults, promoting teamwork and empathy. Stimulating Creative Thinking and problem-solving activities that encourage imagination and innovation. Participating in physical activities that enhance motor skills, coordination, and overall health.</p>
<p>Fostering Curiosity and Exploration in science and social studies to develop a sense of curiosity about the world.</p>
<p>These outcomes are designed to nurture well-rounded growth, preparing students not only for academic progress but also for personal and social success.</p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/junior-3-4-1-665ab07421afb.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-feature">

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 1: From Tiny Sprout to Vibrant Blossom (3 yrs - 4 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">You can watch your kid grow into a self-assured learner! The focus is on creating a balanced program that nurtures the whole child, preparing them for the next steps in their educational journey. We support their development, help them get over their fears, and kindle their curiosity. We offer concentrated teaching on phonics and numbers. Promoting fine motor skills like running, jumping and kicking through activities. Expanding vocabulary and improving communication skills through conversation, story telling and rhymes. Encouraging interaction with peers to develop social skills such as sharing, cooperation, and empathy.  </p>

</div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/junior-4-5-665ab0769a61f.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-feature">

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 2: Seedlings of Knowledge (4 yrs -  5 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Between the ages of 4 to 5 years, our children learn a variety of skills and knowledge that form the foundation for their future education. Recognizing and writing simple words and beginning to read very short sentences. Learning basic Arithmetic concepts, shapes, sizes and patterns. Learning to share, take turns, and work in groups. Developing Fine Motor Skills like drawing, coloring, tearing and pasting to develop hand-eye coordination, running, jumping, climbing, and other physical activities to build strength and develop balancing. Developing Language of the children by engaging them in conversations, storytelling, and following basic instructions.</p>

</div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/junior-5-6-1-665ab07996b0c.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-feature">

<h1 style="font-family: Papyrus; color: rgb(231 112 81)">Program 3(5 yrs -  6 yrs)</h1>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Mastering Fundamental Skills in reading, writing, and mathematics to build a strong academic foundation.Enhancing verbal and non-verbal communication, fostering the ability to express thoughts and feelings effectively. Cultivating Social Skills to interact positively with peers and adults, promoting teamwork and empathy. Stimulating Creative Thinking and problem-solving activities that encourage imagination and innovation. Participating in physical activities that enhance motor skills, coordination, and overall health.Fostering Curiosity and Exploration in science and social studies to develop a sense of curiosity about the world.These outcomes are designed to nurture well-rounded growth, preparing students not only for academic progress but also for personal and social success.</p>

</div>

<h2 style="font-family: Papyrus;">Facilities and Resources</h1>

<p style="font-family: 'Helvetica'; font-weight:500;">We all want to select the best preschool for our kid's primitive educational years and bulid a strong foundation for child's future. </p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Classroom2.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Classrooms</h2>
<p style="font-family: 'Helvetica'; font-weight:500;">The school includes completely air-conditioned, centralised classrooms with a capacity of 25–30 students each, equipped with Smart Class (Digital Teaching System) for efficient learning.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Microscope.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Labs</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">There are dedicated labs for each department in the following areas: biology, chemistry, physics, maths, language, robotics and computers.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Library2.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Library</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">  There are two well-stocked libraries, separately, for the Junior and Senior wing. Expand and gain knowledge from the range of books that include publications, magazines, newspapers, and fiction and non-fiction, all with an amazing index of titles.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Sports2.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Sports</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">In order to prepare our children for the fiercely competitive world of sports, we work hard to provide our students with access to some of Panipat's greatest indoor and outdoor sporting facilities.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Bus.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Transportation</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">The school maintains the best transportation infrastructure in Panipat to provide safe transportation, including a fleet of Air Conditioned school buses with CCTV, standard design, and trained drivers. A transport attendant is on board for the duration of the trip to oversee and monitor.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Security-Shield.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black">Safety & Security</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">For constant safety, the school has installed CCTV cameras both within and outside the campus. </p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Around-the-Globe2.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color: black"> Tours and Excursions</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Our school organizes enriching tours and excursions to enhance student learning. These trips provide practical experiences and exposure to new environments.</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/UNESCO2.png" alt="" style="width:44px;height:auto;display:inline-block">

<h2 style="font-family: Papyrus; color:black">UNESCO</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">Celebrating International Days and Years with a focus on AIDS prevention, cultural preservation, and literacy. We believe in working together to empower the next generation for a more optimistic and health-conscious world</p>

</div>

<h2 style="font-family: Papyrus;">Facilities and Resources</h1>

<p style="font-family: 'Helvetica'; font-weight:500;">We all want to select the best preschool for our kid's primitive educational years and bulid a strong foundation for child's future. </p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/02/Student-teaching.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="font-family: Papyrus; color: black">Classrooms</h2>
<p style="font-family: 'Helvetica'; font-weight:500;">The school includes completely air-conditioned, centralised classrooms with a capacity of 25–30 students each, equipped with Smart Class (Digital Teaching System) for efficient learning.</p>

<h2 style="font-family: Papyrus; color: black">Library</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">  There are two well-stocked libraries, separately, for the Junior and Senior wing. Expand and gain knowledge from the range of books that include publications, magazines, newspapers, and fiction and non-fiction, all with an amazing index of titles.</p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/libraryl_prayaag_int_school_panipat.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/Transport.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="font-family: Papyrus; color: black">Transportation</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">The school maintains the best transportation infrastructure in Panipat to provide safe transportation, including a fleet of Air Conditioned school buses with CCTV, standard design, and trained drivers. A transport attendant is on board for the duration of the trip to oversee and monitor.</p>

<h2 style="font-family: Papyrus; color: black">Safety & Security</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">For constant safety, the school has installed CCTV cameras both within and outside the campus. </p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/Safety.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/trip-665ab07d7056d.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h2 style="font-family: Papyrus; color: black;">Trips and Excursions</h2>
<p style="font-family: 'Helvetica'; font-weight: 500;">Our school organizes enriching trips to provide young learners with a break from their usual environment and an opportunity to experience new activities or places.</p>

<h2 style="font-family: Papyrus; color: black">Sports</h2>
<p 

<p style="font-family: 'Helvetica'; font-weight:500;">In order to prepare our children for the fiercely competitive world of sports, we work hard to provide our students with access to some of Panipat's greatest indoor and outdoor sporting facilities.</p>

<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/06/3t0a0436-665ab071b77d2.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

</div></div>

<h6>Join us now</h6>

<h2 style="font-family: Papyrus;">Faculty and Staff:</p>
</h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/06/junior-staff-dsc-0768-scaled-665ab07258d8b-1.webp" alt="" loading="lazy">

<h2 style="font-family: Papyrus;">Testimonials </p>
</h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image1-2.jpeg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>We were first concerned about our child's transfer to Prayaag International School as new parents, but we have found the school to be quite friendly and accommodating. Our child has adapted rapidly and looks forward to attending school every day. The range of activities offered and the caliber of instruction are impressive. We are sure that our decision was the right one.</p>

<h4 style="margin-bottom: 0px;"> Ms Pooja </h4>
<p><span style="font-size: 80%;"><B>M/o</B> Mehar  Grade Nursery Grade I</p>
<p></span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image2.jpeg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>Enrolling our child at Prayaag International School was a big decision for us, and we are thrilled with the outcome. The orientation process was thoroughly, and the communication from school has been excellent. Our child felt comfortable and excited from the very beginning, thanks to the warm and nurturing environment of the school.</p>

<h4 style="margin-bottom: 0px;"> Ms Isha Saluja </h4>
<p><span style="font-size: 80%;"><B>M/o</B> Manan Saluja Grade Kg</p>
<p></span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image3.jpg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>We have a great experience with Prayaag International School Panipat. I am very grateful to all teachers, coordinator and Principal.  My daughter Advika Singhal is being groomed very well here. The school  is doing really well and with the support of dedicated class teachers my daughter is doing great.I wish great success to the PISP group!"</p>

<h4 style="margin-bottom: 0px;">Mother of Aadvika Singal</p>
</h4>
<p><span style="font-size: 80%;">Grade Kg</p>
<p></span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image4.jpeg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>The school is good, and my child Ruhi's academic performance is becoming better.I appreciate the school's Principal and staff for providing such a wonderful education.My daughter has improved a lot.She has become more diligent and I am completely satisfied."</p>

<h4 style="margin-bottom: 0px;"> Ms Mamta<br />
 </h4>
<p><span style="font-size: 80%;">Mother of Ruhi, 3H
</p>
<p></span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image5.jpeg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>Our experience at Prayaag International School has been amazing .The day I got my kids admitted , I was so worried and concerned but in a span of one month I was happy  that I choose Prayaag.The school and specially the staff shows so much commitment and efforts towards every child in the best  possible ways. My children are showing progress in academics , art, and crafts. They are socialising so well with others.We are proud to be a part of Prayaag International School."</p>

<h4 style="margin-bottom: 0px;"> Mother of Adhvik and Aarav</p>
</h4>
<p><span style="font-size: 80%;">Grade Nursery H</p>
<p></span></p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/image6.jpeg" alt="" loading="lazy">

<h2><span style="font-size: 300%;">"</span></h2>

<p>I'm happy with my child's education and the school's attempts to help him learn. The teaching team is amiable and helpful as well. The school keeps up a good level of discipline. Extracurricular pursuits are also greatly valued. We appreciate that teachers are always just a phone call away and can resolve any problem quickly. All things considered, I am quite happy that we chose to send our daughter to Prayaag International School.</p>

<h4 style="margin-bottom: 0px;"> Mother of Anvie Kharb
</p>
</h4>
<p><span style="font-size: 80%;">Grade 3H
</p>
<p></span></p>

<h2 style="font-family: Papyrus;">Google Reviews</p>
</h2>

<h2 style="font-family: Papyrus;">Send us a message</p>
</h2>
PISPHTML,
            ],
            [
                'slug' => 'summer-camp',
                'title' => 'Summer Camp',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => true,
                'html' => <<<'PISPHTML'
<p style="margin-bottom:0px;">Summer Camp Adventure Awaits!</p>

<p>School Summer Adventure Awaits: Enriching, Exciting, and Educational Camp Experiences for Young Explorers!</p>

<h2>Get in Touch</h2>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/1b4e08ee07fd06eea5fd8740df032c65-moon-and-stars-sky-flat.webp" alt="" loading="lazy">

<p style="font-size:40px; font-weight:900; color:black; text-align:center; margin-bottom:10px;">About Us</p>

<p>Prayaag International School, located in the heart of Panipat, is more than just an educational institution; it’s a nurturing ground for young minds, a place where aspirations are nurtured and potential is shaped. Since its inception in 2016, the school has been dedicated to providing holistic education that goes beyond textbooks, fostering a stimulating environment where students can excel academically, emotionally, and socially.</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Emojione1_1F3B7.svg.png" alt="" loading="lazy">

<p style="margin-bottom:10px;">Exciting Adventures Await</p>

<p style="font-size:40px; font-weight:900; color:#ffff; text-align:center; line-height:45px; margin-bottom: px;">From Outdoor Exploration To Creative Workshops</p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/nature_care.svg" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Nature Exploration</h3>
<li style="margin-left:0px;">Guided nature walks</li>
<li style="margin-left:0px;"> Birdwatching sessions</li>
<li style="margin-left:0px;">Outdoor scavenger hunts</li>
<li style="margin-left:0px;" >Environmental education</li>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/paint_brush.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>
<h3>Arts & Crafts</h3>
<li style="margin-left:0px;">Painting and drawing </li>
<li style="margin-left:0px;">DIY craft projects</li>
<li style="margin-left:0px;">Pottery and ceramics </li>
<li style="margin-left:0px;">Collaborative mural</li>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/golf_hole.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>
<h3>Sports &amp; Games</h3>
<ul>
<li style="margin-left: 15px;">Fun games</li>
<li style="margin-left: 15px;">Team Athletics</li>
<li style="margin-left: 15px;">Relay Fun</li>
<li style="margin-left: 15px;">Group Challenges</li>
</ul>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/robotic.svg" alt="" style="width:44px;height:auto;display:inline-block">

</div>
<h3>STEM Exploration</h3>
<ul>
<li style="margin-left: 15px;">Robotics workshops</li>
<li style="margin-left: 15px;">Science experiments</li>
</ul>

<p style="font-size:40px; font-weight:900; color:#000; text-align:center; line-height:50px;  margin-bottom:50px;">Plan Your Child's Perfect Summer Adventure</p>

<ul style="line-height: 100%;">

<li style="font-weight:900;" class="bullet-disk">From: 22nd May to 31st May</li>

</ul>

<ul style="line-height: 100%;">

<li style="font-weight:700; class="bullet-Disk">Time: 7:45 AM to 10:30 AM</li>

</ul>

<ul style="line-height: 100%;">

<li style="font-weight:700; class="bullet-Disk">Charges: Rs. 1500/- Per Student</li>

</ul>

<h4>Summer Camp Schedule For Classes Pre - Nursery - II</h4>
<p>Choose Any One From Activity 1 & Activity 2</p>

<table border="1">
    <tr>
      <th>Activity 1</th>
      <th>Capacity</th>
      <th>School Transport</th>
    </tr>
    <tr>
      <td>Cricket</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Martial Arts</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Football</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Skating</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Clay</td>
      <td>30</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Phonics</td>
      <td>30</td>
      <td>yes</td>
    </tr>
  </table>

<table>
    <tr>
      <th>Activity 2</th>
      <th>Capacity</th>
      <th>School Transport</th>
    </tr>
    <tr>
      <td>Art & Craft</td>
      <td>30</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Dance- Classical/Western</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Swimming</td>
      <td>60</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Music-Instrumental</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Calligraphy</td>
      <td>60</td>
      <td>yes</td>
    </tr>
    
  </table>

<h4>Summer Camp Schedule For Classes III - VIII</h4>
<p>Choose Any One From Activity 1 & Activity 2</p>

<table>
    <tr>
      <th>Activity 1</th>
      <th>Capacity</th>
      <th>School Transport</th>
    </tr>
    <tr>
      <td>Art & Craft</td>
      <td>30</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Dance- Classic/Western</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Budding Scientists</td>
      <td>30</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Music Instrumental</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Coding(Only for VI-VIII)</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Fireless Cooking</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Swimming</td>
      <td>60</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Calligraphy</td>
      <td>25</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Memory Enhancement</td>
      <td>20</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Public Speaking</td>
      <td>30</td>
      <td>yes</td>
    </tr>

    <tr>
      <td>Shooting</td>
      <td>20</td>
      <td>yes</td>
    </tr>
  </table>

<table>
    <tr>
      <th>Activity 2</th>
      <th>Capacity</th>
      <th>School Transport</th>
    </tr>
    <tr>
      <td>Cricket</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Martial Arts</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Football</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Basketball</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Laws Tennis</td>
      <td>20</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Volleyball</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Skating</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Robotics</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Badminton</td>
      <td>35</td>
      <td>yes</td>
    </tr>
    <tr>
      <td>Table Tennis</td>
      <td>30</td>
      <td>yes</td>
    </tr>

    <tr>
      <td>Shooting</td>
      <td>20</td>
      <td>yes</td>
    </tr>

    
    <tr>
      <td>Memory Enhancement</td>
      <td>20</td>
      <td>yes</td>
    </tr>
  </table>

<h5>Safety & Supervision</h5>

<p>Trained Staff<br />
Health & Medical Support Supervision Ratio<br />
Safety Policies & Procedures</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/protect.svg" alt="" loading="lazy">

<p style="margin-bottom:10px; margin-top:40px;">FAQ</p>

<p style="font-weight:700;">Everything You Need To Know</p>

<p>Our summer camp is open to students aged 3 years to 13 years, typically encompassing (Pre Nur to VIII) students.</p>

<p>We offer a diverse range of activities including outdoor sports, arts and crafts, team-building exercises, educational workshops, and more. Our goal is to provide a well-rounded experience that caters to various interests.</p>

<p>The safety of our campers is our top priority. We have trained staff members who oversee all activities, conduct regular safety checks of equipment and facilities, and follow strict protocols for emergencies. Additionally, we maintain a low camper-to-staff ratio to ensure individual attention and supervision.</p>

<p>Registration for our summer camp can be completed online through our website. Simply fill out the form and you will receive confirmation and additional details about the camp.</p>

<p>School transport will be provided on regular routes.</p>

<p style="margin-bottom:10px; margin-top:40px;">Testimonials</p>

<p style="font-weight:700;">Words From Happy Parents</p>

<p class="lead">"The summer camp was a fantastic experience for my son. My child learnt so much and had a great time doing it. We'll definitely be back next year!"</p>

<p class="lead">Anjali Piplani</p>

<p class="lead">"I can't say enough good things about the summer camp. The staff was caring and attentive, and my child had a blast. It was the highlight of his summer!"</p>

<p class="lead">Pooja Sharma</p>

<p class="lead">"The summer camp exceeded my expectations. Not only did my child have a great time, but he also learned valuable skills and made lasting friendships. It was a commendable effort of the school."</p>

<p class="lead">Devika</p>

<p class="lead">"My child had an amazing time at the summer camp! She came home every day with stories of new friends and exciting activities where she learnt and enjoyed . Thank you for creating such a fun and safe environment."
</p>

<p class="lead">Ritika Sharma</p>

<p class="lead">"The summer camp was an incredible experience for our son, Aarav! Every day, he returned home with tales of his thrilling new adventures and new pals. I appreciate you making this place so enjoyable and secure."</p>

<p class="lead">Jyoti sharma</p>

<p class="lead">"Our family had an amazing time at the summer camp. Diya, our kid, enjoyed herself much while learning a lot. Of course, we'll return the following year!"
</p>

<p class="lead">Ajay Goel</p>

<h6>School Memories</h6>

<p style="font-size:40px; font-weight:900; color:#000; text-align:center; line-height:40px; margin-bottom: px;"> Explore Our Gallery</p>

<div class="imp-gallery"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/349561532_746749217151044_7216771633330101304_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/349344872_209198325263916_4248956306377832658_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/348939872_916254722968762_5042635852759667121_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/348902451_540614124729527_8987312009068906754_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/348594861_266057195807308_4071572129868493684_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/348474210_627189676100462_7331555763719460901_n-1.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/20230531_100250-scaled.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/351142826_948332089721273_862021189224700167_n.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"></div>

<h2>Connect With Us</h2>

<p style="font-size:17px; font-weight:6900; color:#000; text-align:left; line-height:25px; margin-bottom: 20px; margin-top: 10px;"> 

Prayaag International School Opp. <br>New Police Lines NH-44, Panipat

</p>

<p style="font-size:20px; font-weight:900; color:#000; text-align:left; line-height:15px; margin-bottom: px;"> Contact</p>

<p style="font-size:17px; font-weight:6900; color:#000; text-align:left; line-height:20px; margin-bottom: 20px; margin-top: 10px;"> 

+919350748851, +91180-2565555, 2575555

</p>

<p style="font-size:20px; font-weight:900; color:#000; text-align:left; line-height:15px; margin-bottom: px;"> Opening Hours</p>

<p style="font-size:17px; font-weight:6900; color:#000; text-align:left; line-height:30px; margin-bottom: 20px; margin-top: 10px;"> 

Mon-Sat – 07:45 AM – 03:30 PM<br>
Sunday Closed

</p>

<h2>Get in Touch</h2>
PISPHTML,
            ],
            [
                'slug' => 'senior-landing-page',
                'title' => 'Senior Landing Page',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => true,
                'html' => <<<'PISPHTML'
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PRCHBJ46"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<p style="margin-bottom:0px;">Empowering Young Minds for a Brighter Tomorrow</p>

<p style="margin-bottom:0px;">

<p style="margin-bottom:0px;">Enroll At Prayaag</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Basketball-Practice.webp" alt="" loading="lazy">

<p style="margin-bottom:0px;">Admission Process</p>

<p style="margin-bottom:0px;"</p>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/vidya-modern-knowldge-icon-1.png" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Filling Application Form</h3>
<p> <a href="#" style="background-color:#446084; color:#fff; padding:10px 20px; border-radius:5px;">Fill Application Online </a></p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Attend-Counselling-1.png" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Attend Counselling</h3>
<p> Campus Tour, Entrance Test, Parents & Students Interaction</p>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Registration-of-Admission-1.png" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Registration of Admission</h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/vidya-modern-leadership-icon-1.png" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Confirmation of Admission</h3>

</div>

<div class="imp-feature"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/Congratulations-1.png" alt="" style="width:44px;height:auto;display:inline-block">

<h3>Congratulations!</h3>
<p>Now your child is Prayaagian!</p>

</div>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/diverse-business-partners-reading-contract-together-Large.jpg" alt="" loading="lazy">

<p style="margin-bottom:20px;">Documents Required for Admission</p>

<ul>
<li>6 Passport size photographs of the student</li>
<li>3 Passport size photographs of the mother</li>
<li>3 Passport size photographs of the father</li>
<li>1 Passport size photograph of guardian (if any)</li>
<li>Birth certificate of child</li>
<li>Cop of Aadhar cards- Student, Father, Mother & Guardian</li>
<li>Report card of previous class</li>
<li>Online SLC of previous class (Grade 1 onwards) Manual SLC of previous class (Grade Nursery-KG)</li>
<li>Family ID</li>
</ul>

<p style="margin-bottom:20px;">Start Online Admission Process Now</p>

<p>Prayaag International School is committed to providing a seamless and open admission to deserving childern. We make sure that every process is clear and simple for everyone involved. Here is a thorough guide to assist you through each step.</p>

<a class="imp-btn" href="#">registration form</a>

<p style="margin-bottom:20px;">Facilities and Resources</p>

<p style="margin-bottom:0px;">State-of-the-Art Facilities and Premier Resources for Holistic Development</p>

<h2 style="color: black">Classrooms</h2>
<p>Our classrooms are vibrant, interactive spaces equipped with modern technology to enhance learning. We foster a supportive and inclusive environment where every student can thrive and reach their full potential.</p>

<h2 style="color: black">Playgrounds</h2>
<p>Our expansive school grounds feature dedicated areas for a variety of sports, including soccer, basketball, cricket, Lawn Tennis, Volleyball, and track and field. These well-maintained facilities with ample opportunities for physical activity and team-building.</p>

<h2 style="color: black">Swimming Pool</h2>
<p>Our state-of-the-art swimming pool offers a safe and enjoyable environment for students to learn and practice swimming. With professional coaching, we ensure every student can develop their aquatic skills and confidence.</p>

<h2 style="color: black">Shooting Range</h2>
<p>Our shooting range provides a secure and controlled environment for students to develop precision and discipline. With expert instruction and top-notch safety measures, we foster skill development in this unique sport.</p>

<h2 style="color: black">Library</h2>
<p>Our senior school library is a hub of knowledge and discovery, stocked with a diverse collection of books and research materials. It offers a quiet, comfortable space for students to study, explore, and engage in independent learning.</p>

<h2 style="color: black">Laboratories</h2>
<p>Our state-of-the-art laboratories are equipped with cutting-edge technology and tools, providing students with hands-on experience in science and experimentation. These dynamic learning environments foster innovation, critical thinking, and a deep understanding of scientific principles.</p>

<h2 style="color: black">Transportation</h2>
<p>Our school offers efficient transportation services, ensuring students arrive safely and punctually with our fleet of well-maintained Air-Conditioned vehicles and experienced drivers.</p>

<h2 style="color: black">MUN</h2>
<p>Our school's Model United Nations (MUN) program empowers students to engage in global issues through simulation of UN conferences, fostering diplomacy, negotiation, and public speaking skills.</p>

<h2 style="color: black">Performing Arts</h2>
<p>Our Dance and Music programs provide students with the opportunity to explore artistic expression, enhance creativity, coordination, and cultural appreciation, enriching the overall educational experience.</p>

<h2 style="color: black">Visual Art</h2>
<p>Our Art and Craft program nurtures creativity and craftsmanship through diverse projects, encouraging students to explore various mediums and express themselves artistically. It fosters imagination, fine motor skills, and a deeper appreciation for artistic endeavors.</p>

<h2 style="color: black">Trips and Tours</h2>
<p>Embark on educational journeys with our Tours and Excursions program, where students discover new perspectives and enrich their learning through engaging experiences outside the classroom.</p>

<h2 style="color: black">Parents School Partnership</h2>
<p>Our school hosts a dynamic array of events throughout the year, from cultural festivals and sports meets to academic competitions and art exhibitions. These events foster community spirit, showcase student talents, and enrich the educational experience for everyone involved.</p>

<h2 style="color: black">Media and Press</h2>
<p>Our Media and Press section provides journalists and community members with access to our latest news, press releases, and highlights from events at the school. This hub is designed to keep the community informed and engaged with our ongoing activities and achievements.</p>

<h2 style="color: black">Safety and Security</h2>
<p>Campus Safety policy and process in our school is achieved through safe building structure, safe electrical installations, continuous CCTV surveillance, physical perimeter safety, and adherence to strict visitor management policy. Our school is equipped with essential Fire fighting facilities backed by periodic training and mock drills to evaluate readiness to handle emergencies.</p>

<p style="margin-bottom:20px;">Our Gallery</p>

<p style="margin-bottom:0px;">Visit our school's gallery to explore a creative and evolving environment. Through<br />
engaging workshops, classes, and activities, see how our students' talents develop!</p>

<div class="imp-gallery"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Alumni.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Biology-Lab-Prayaag-International-School.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Math-Lab-Prayaag-International-School.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Computer-Lab-Prayaag-International-School.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/skating_prayaag_international_panipat.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Chemistry-Lab-Prayaag-International-School.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Physics-Lab-Prayaag-International-School.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/cricket_prayaag_international_panipat.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/judo_karate_prayaag_international_panipat.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/04/Shooting-Range-Prayaag-International-School-Panipat.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/lawn_tennis_prayaag_international_panipat.jpg" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"><img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Teacher-teaching-keyboard.webp" alt="" loading="lazy" style="display:inline-block;width:31%;margin:1%"></div>

<p style="margin-bottom:20px;">Testimonials</p>

<p style="margin-bottom:40px;">Voices of Excellence: Hear from Our Global Community</p>

<h3>vaibhav solanki</h3>
<p>With sound infrastructure and an appealing environment, Prayag International is one of the best schools in the city. Use of specialized innovative technology and advanced teaching methods are the driving forces to nurture kids and makes learning fun.</p>

<h3>Alka Batra</h3>
<p>The vibrant atmosphere at Prayaag International School fosters a rich learning environment. With dedicated educators and diverse extracurricular activities, it nurtures holistic development. The school's modern facilities, emphasis on innovation, and commitment to academic excellence make it a standout choice for comprehensive education.<br />
Like</p>

<h3>Jyoti Aghi</h3>
<p>Awesome environment! Full of greenery! Subject enrichment activities! Celebrating festivals with great enthusiasm! Classrooms available with smart board always ready to use!<br />
Love my school</p>

<p style="margin-bottom:20px;">Our Faculty</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/06/DSC_0770-1.webp" alt="" loading="lazy">

<p style="margin-bottom:20px;">FAQ’s</p>

<p style="margin-bottom:40px;">This are the common FAQ's asked by our parents, just to make you ease our team has answered few of them here, hope this will help you.</p>

<p>Parents receive updates from the school through Emails and Newsletters about school events and announcements, Parent-Teacher Meetings to discuss a child's progress and behaviour, School Websites and Portals for Online access to attendance and assignments.<br />
Text/Class Whatsapp Groups or Voice calls. Moreover, the school news is shared on platforms like Facebook and Instagram.</p>

<p>The School updates students about careers and higher education through Career Counselling and Guidance Sessions.<br />
 The School organizes events where students can meet representatives from colleges, universities, and various industries to learn about different career paths and higher education opportunities.<br />
The School arranges visits to colleges and universities to give students a first-hand look at campus life and academic programs.<br />
Regular sessions are held where teachers provide updates and information about career planning and college readiness.</p>

<p>The School offers a wide range of extracurricular activities that support students' academic progression and overall development.<br />
 Sports likeBadminton, Table Tennis, Lawn Tennis, Basketball,Soccer, Volleyball, Cricket, Skating, Shooting, Martial Arts, Swimming, etc. improve organizational skills by balancing activities and academics.<br />
Robotics, Choir,School band,Nukkad Naatak, Visual arts, Dance etc. improve well-being and reduce stress, build collaboration and communication skills.</p>

<p>Our school adheres to the CBSE, which aims to offer a thorough and well-rounded<br />
education with an emphasis on the whole development of the child.</p>

<p>Send us a message</p>
PISPHTML,
            ],
            [
                'slug' => 'thank-you-for-senior',
                'title' => 'Thank You for senior',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PRCHBJ46"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<h1 class="uppercase">THANK YOU FOR YOUR ENQUIRY</h1>
<h1 class="uppercase">Your admission enquiry has been successfully submitted.</h1>
<p class="lead">Our admission team will contact you shortly to guide you through the admission process for the 2026-27 academic session. If you need immediate assistance, please call or WhatsApp us.</p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/">Learn more</a>

<a href="tel:+919350748851"><button class="image-button">
        <img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/phone_480px.png" alt="Button Image" class="button-image">
        Call Now
    </button>
</a>

<style>
.image-button {
       display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    font-size: 13px;
    background-color: 
#ffff!important;
    color:#000!important;
    border: none;
    border-radius: 5px;
    cursor: pointer;
padding:10px 0px!important;
}

.button-image {
    margin-right: 10px;
    width: 30px; /* Adjust size as needed */
   
}

.image-button:hover {
    background-color: #45a049;
}</style>

<a href="https://wa.me/+91935074885"><button class="whatsapp-button">
        <img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/whatsapp_480px.png" alt="Button Image" class="button-image">
        WhatsApp Now
    </button>
</a>

<style>
.whatsapp-button {
       display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    font-size: 13px;
    background-color: 
#ffff!important;
    color:#000!important;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding:10px 0px!important;
}

.button-image {
    margin-right: 10px;
    width: 30px; /* Adjust size as needed */
   
}

.whatsapp-button:hover {
    background-color: #45a049;
}</style>

<p>Stay informed – follow us!</p>

<script>
  gtag('event', 'conversion', {'send_to': 'AW-11175888819/ET_vCJyL7o0cELOPitEp'});
</script>
PISPHTML,
            ],
            [
                'slug' => 'thank-you-for-summer-camp',
                'title' => 'Thank You for summer camp',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2024/04/best-schools-in-samalkha.jpg');background-size:cover;background-position:center"><div class="imp-hero-in">

<h1 class="uppercase">Thank you for submitting</h1>
<p class="lead">Thank you for registering your child for Summer Camp! We are thrilled to have them join us for an exciting and unforgettable experience.</p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/">Learn more</a>

</div></div>
PISPHTML,
            ],
            [
                'slug' => 'thank-you',
                'title' => 'Thank You for Junior',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQVDJDH7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<h1 class="uppercase">Thank you for submitting</h1>
<p class="lead">Thank you for joining our Junior Program at Prayaag International School, Panipat. We value your interest and look forward to guiding your child towords academic excellence and personal growth. Please feel free to reach out to us at <a style="color:white;" href="tel:+919350748851">+91 93507 48851</a> or +91 180 256 5555 / 257 5555. We're here to ensure your experience with us is exceptional.</p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/">Learn more</a>

<a href="tel:+919350748851"><button class="image-button">
        <img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/phone_480px.png" alt="Button Image" class="button-image">
        Call Now
    </button>
</a>

<style>
.image-button {
       display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    font-size: 13px;
    background-color: 
#ffff!important;
    color:#000!important;
    border: none;
    border-radius: 5px;
    cursor: pointer;
padding:10px 0px!important;
}

.button-image {
    margin-right: 10px;
    width: 30px; /* Adjust size as needed */
   
}

.image-button:hover {
    background-color: #45a049;
}</style>

<a href="https://wa.me/+91935074885"><button class="whatsapp-button">
        <img src="https://prayaaginternationalschool.com/wp-content/uploads/2024/05/whatsapp_480px.png" alt="Button Image" class="button-image">
        WhatsApp Now
    </button>
</a>

<style>
.whatsapp-button {
       display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    font-size: 13px;
    background-color: 
#ffff!important;
    color:#000!important;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding:10px 0px!important;
}

.button-image {
    margin-right: 10px;
    width: 30px; /* Adjust size as needed */
   
}

.whatsapp-button:hover {
    background-color: #45a049;
}</style>

<p>Stay informed – follow us!</p>
PISPHTML,
            ],
            [
                'slug' => 'alumni-new',
                'title' => 'Alumni-New',
                'seo_title' => 'Prayaag International School, Panipat Alumni | Shaping Futures, Inspiring Excellence',
                'seo_desc' => 'Explore the accomplished alumni of Prayaag International School in Panipat. Discover their inspiring stories of success and how they continue to shape the world around them.',
                'form' => true,
                'html' => <<<'PISPHTML'
<div class="imp-hero" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp');background-size:cover;background-position:center"><div class="imp-hero-in">

<h3 class="uppercase"><strong>Alumni</strong></h3>

</div></div>

<h2 style="text-align: center;"><span style="font-size: 150%; color: #f99b1c;">Alumni</span></h2>

<h2 style="color: #000000;text-align: center;">Welcome to the Prayaag International School, Panipat Alumni Page</h2>
<p style="color: #000000;text-align: justify;">At Prayaag International School, we take immense pride in our alumni community – a network of individuals who have not only excelled in their chosen fields but have also carried the values and ethos of our institution to every corner of the world. This page is a dedicated space to celebrate your achievements, reconnect with old friends, and continue being a part of the vibrant Prayaag family.</p>

<img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Alumni.jpg" alt="" loading="lazy">

<h3 style="color: #000000;text-align: center;">Stay Connected</h3>
<p style="color: #000000; text-align: justify;">We believe that the bond between the school and its alumni is everlasting. Stay connected with us to keep up with the latest happenings, events, and developments at Prayaag International School. Update your contact information and follow us on social media to receive updates about reunions, workshops, and other exciting opportunities to reconnect.</p>

<h3 style="color: #000000;text-align: center;">Share Your Journey</h3>
<p style="color: #000000; text-align: justify;">Your journey since leaving Prayaag International School is a story worth sharing. We invite you to share your experiences, accomplishments, and milestones with us. Whether it's a groundbreaking project, a new business venture, or a personal achievement, your story can inspire the current students and fellow alumni.</p>

<div class="imp-feature">

<h3 style="color: #000000;text-align: center;">Giving Back</h3>
<p style="color: #000000; text-align: justify;">As alumni, you are an integral part of our school's legacy. Your support can make a difference in the lives of current students. Whether through scholarships, guest lectures, or mentoring programs, your contribution can shape the next generation of leaders and thinkers.</p>

</div>

<p>For any queries, suggestions, or to share your updates, please contact our Alumni Relations team at alumni@pisp.in</p>

<a class="imp-btn" href="https://prayaaginternationalschool.com/contact-us/">Mail Us</a>

<h4 class="uppercase">Reunions and Events</h4>
<p style="color: #000000; text-align: justify;">Reunions are a perfect opportunity to relive the memories, create new ones, and reconnect with classmates and teachers. Keep an eye on this section for updates about upcoming reunions and events. Don't miss the chance to come back to where it all began.</p>

<p style="color: #000000; text-align: justify;">To ensure you don't miss out on any updates, please keep your contact information updated. Let us know about your achievements and milestones so we can celebrate them together. You can also share your feedback and suggestions to help us improve the alumni experience.</p>

<p style="color: #000000; text-align: justify;">Thank you for being an integral part of the Prayaag International School family. Your journey is an inspiration to us all, and we look forward to celebrating your continued success. Stay connected, stay engaged, and keep the Prayaag spirit alive!</p>

<h2>Get in touch</h2>

<p>Lorem ipsum dolor sit amet consectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolores cannon adipiscing magna aliquam erat volutpat.</p>
PISPHTML,
            ],
            [
                'slug' => 'landing-page-google-ads',
                'title' => 'Landing Page Google Ads',
                'seo_title' => '',
                'seo_desc' => '',
                'form' => false,
                'html' => <<<'PISPHTML'
<header class="header">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;width:100%">
      <div class="logo-section">
        <img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/prayaag-school-logo.png" alt="Prayaag Logo" class="logo">
        <div class="brand">
          Prayaag International School
          <span>Panipat, Haryana</span>
        </div>
      </div>
      <div class="header-cta">
        <a href="tel:+919350748851" class="btn-call">
          <i class="fas fa-phone"></i> Call
        </a>
        <a href="https://wa.me/919350748851" class="btn-whatsapp" target="_blank">
          <i class="fab fa-whatsapp"></i> Chat
        </a>
      </div>
    </div>
  </header>

  
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <div class="hero-badge">
          <i class="fas fa-award"></i> Admissions Open 2026-27
        </div>
        <h1>Best CBSE School in Panipat</h1>
        <p>Top Rated School Near Samalkha | Smart Classes • Expert Faculty • Safe Campus • Holistic Development</p>
        
        <div class="hero-ctas">
          <a href="tel:+919350748851" class="btn-call">
            <i class="fas fa-phone-alt"></i> Call Now
          </a>
          <a href="https://wa.me/919350748851" class="btn-whatsapp" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp
          </a>
        </div>

        <div class="form-card">
          <h3><i class="fas fa-calendar-check"></i> Book Free Campus Visit</h3>
          <form action="/thank-you" method="POST">
            <div class="form-group">
              <input type="text" name="student_name" placeholder="Student Name *" required>
            </div>
            <div class="form-group">
              <input type="tel" name="mobile" placeholder="Mobile Number *" required pattern="[0-9]{10}">
            </div>
            <div class="form-group">
              <select name="grade" required>
                <option value="">Select Grade *</option>
                <option>Nursery - KG</option>
                <option>Class 1 - 5</option>
                <option>Class 6 - 8</option>
                <option>Class 9 - 10</option>
                <option>Class 11 - 12</option>
              </select>
            </div>
            <button type="submit" class="btn-submit">
              <i class="fas fa-paper-plane"></i> Submit Request
            </button>
            <p class="form-note">🔒 Your details are secure. We'll contact you within 24 hours.</p>
          </form>
        </div>
      </div>
    </div>
  </section>

  
  <section class="trust-section">
    <div class="container">
      <div class="trust-grid">
        <div class="trust-item"><i class="fas fa-trophy"></i> Best School in Panipat</div>
        <div class="trust-item"><i class="fas fa-book"></i> CBSE Affiliated</div>
        <div class="trust-item"><i class="fas fa-star"></i> 10+ Years Excellence</div>
        <div class="trust-item"><i class="fas fa-map-marker-alt"></i> Near Samalkha, NH-44</div>
      </div>
    </div>
  </section>

  
  <section class="gallery-section">
    <div class="container">
      <div class="section-title">
        <h2>Campus Life at Prayaag</h2>
        <p>Experience world-class infrastructure designed for holistic learning</p>
      </div>
      <div class="carousel">
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1588072432836-e10032774350" alt="Smart Classroom">
          <div class="carousel-caption">🎓 Smart Digital Classrooms</div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7" alt="Science Lab">
          <div class="carousel-caption">🔬 Advanced Science Labs</div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1571212515416-f75c3e8b8d3d" alt="Sports">
          <div class="carousel-caption">⚽ Sports & Activities</div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1543269664-76bc3997d9ea" alt="Library">
          <div class="carousel-caption">📚 Resource-Rich Library</div>
        </div>
      </div>
    </div>
  </section>

  
  <section class="container">
    <div class="results-banner">
      <h2><i class="fas fa-chart-line"></i> Outstanding Academic Results</h2>
      <p>Consistently delivering excellence in CBSE Board Examinations</p>
      <div class="results-stats">
        <div class="stat-item">
          <span class="stat-number">95%+</span>
          <span class="stat-label">Board Pass %</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">98%</span>
          <span class="stat-label">Topper Score</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">100%</span>
          <span class="stat-label">College Placement</span>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section">
    <div class="container">
      <div class="card">
        <h3><i class="fas fa-school"></i> About Prayaag International School</h3>
        <p>
          Prayaag International School is one of the <strong>top CBSE schools in Panipat</strong>, 
          providing quality education with a focus on academic excellence, character building, 
          and 21st-century skills. If you're searching for a <strong>school near me</strong> or 
          <strong>best school in Samalkha</strong>, we offer the perfect blend of tradition and innovation.
        </p>
      </div>

      <div class="card">
        <h3><i class="fas fa-laptop-code"></i> Top Facilities</h3>
        <div class="facilities-grid">
          <div class="facility-item"><i class="fas fa-check-circle"></i> Smart Digital Classrooms</div>
          <div class="facility-item"><i class="fas fa-check-circle"></i> Advanced Science & Computer Labs</div>
          <div class="facility-item"><i class="fas fa-check-circle"></i> Sports Complex & Playground</div>
          <div class="facility-item"><i class="fas fa-check-circle"></i> 24/7 CCTV Surveillance</div>
          <div class="facility-item"><i class="fas fa-check-circle"></i> Transport with GPS Tracking</div>
          <div class="facility-item"><i class="fas fa-check-circle"></i> Hygienic Cafeteria</div>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section" style="background:#f8fafc">
    <div class="container">
      <div class="section-title">
        <h2>Academic Programs</h2>
        <p>Curriculum designed for holistic development and future readiness</p>
      </div>
      <div class="card">
        <div class="programs-grid">
          <div class="program-card">
            <h4><i class="fas fa-seedling"></i> Early Years</h4>
            <p>Nursery to Class 2: Play-based learning, foundational literacy & numeracy</p>
          </div>
          <div class="program-card">
            <h4><i class="fas fa-book-open"></i> Middle School</h4>
            <p>Class 3-8: Concept clarity, critical thinking, co-curricular integration</p>
          </div>
          <div class="program-card">
            <h4><i class="fas fa-graduation-cap"></i> Secondary</h4>
            <p>Class 9-10: CBSE curriculum, board prep, career guidance</p>
          </div>
          <div class="program-card">
            <h4><i class="fas fa-user-graduate"></i> Senior Secondary</h4>
            <p>Class 11-12: Science/Commerce/Arts streams, competitive exam support</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section">
    <div class="container">
      <div class="card">
        <h3><i class="fas fa-clipboard-list"></i> Simple Admission Process</h3>
        <div class="steps-container">
          <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
              <h4>Enquiry & Counselling</h4>
              <p>Connect with our counsellors via call, WhatsApp or campus visit</p>
            </div>
          </div>
          <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
              <h4>Registration & Interaction</h4>
              <p>Submit documents and attend a friendly student-parent interaction</p>
            </div>
          </div>
          <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
              <h4>Confirmation</h4>
              <p>Complete formalities and secure your child's seat with fee payment</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section" style="background:#f8fafc">
    <div class="container">
      <div class="section-title">
        <h2>Voices of Excellence</h2>
        <p>What parents and students say about their Prayaag journey</p>
      </div>
      <div class="carousel">
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="avatar">VS</div>
            <div class="reviewer">
              <h4>Vaibhav Solanki</h4>
              <div class="stars">★★★★★</div>
            </div>
          </div>
          <p>With sound infrastructure and an appealing environment, Prayaag International is one of the best schools in the city. Innovative technology and advanced teaching methods make learning fun.</p>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="avatar">AB</div>
            <div class="reviewer">
              <h4>Alka Batra</h4>
              <div class="stars">★★★★★</div>
            </div>
          </div>
          <p>The vibrant atmosphere fosters rich learning. Dedicated educators, modern facilities, and strong innovation make it a standout choice for holistic development.</p>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="avatar">JA</div>
            <div class="reviewer">
              <h4>Jyoti Aghi</h4>
              <div class="stars">★★★★★</div>
            </div>
          </div>
          <p>Awesome environment full of greenery! Smart classrooms, activities, and celebrations make learning enjoyable. My child looks forward to school every day!</p>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section">
    <div class="container">
      <div class="card">
        <h3><i class="fas fa-question-circle"></i> Frequently Asked Questions</h3>
        
        <div class="faq-item">
          <div class="faq-question">
            <span>What is the admission criteria?</span>
            <i class="fas fa-plus faq-toggle"></i>
          </div>
          <div class="faq-answer">
            Admissions are based on interaction, previous academic records (if applicable), and availability of seats. We follow a transparent, child-friendly process.
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <span>Is transport facility available?</span>
            <i class="fas fa-plus faq-toggle"></i>
          </div>
          <div class="faq-answer">
            Yes, we provide safe, GPS-enabled transport covering Panipat, Samalkha, and nearby areas with trained staff and regular maintenance.
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <span>What co-curricular activities are offered?</span>
            <i class="fas fa-plus faq-toggle"></i>
          </div>
          <div class="faq-answer">
            We offer sports, dance, music, art, robotics, public speaking, yoga, and annual events like fests, exhibitions, and community service projects.
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <span>How can I schedule a campus tour?</span>
            <i class="fas fa-plus faq-toggle"></i>
          </div>
          <div class="faq-answer">
            Simply fill the form above, call us at +91 9350748851, or WhatsApp us. We'll arrange a personalized tour at your convenience.
          </div>
        </div>
      </div>
    </div>
  </section>

  
  <section class="content-section" style="background:#f8fafc">
    <div class="container">
      <div class="card">
        <h3><i class="fas fa-map-marked-alt"></i> Visit Us</h3>
        <div class="contact-grid">
          <div class="contact-info">
            <p><i class="fas fa-map-pin"></i> Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana</p>
            <p><i class="fas fa-phone"></i> <a href="tel:+919350748851">+91 9350748851</a> | <a href="tel:01802565555">0180-2565555, 2575555</a></p>
            <p><i class="fas fa-envelope"></i> <a href="mailto:admissions@prayaaginternationalschool.com">admissions@prayaaginternationalschool.com</a></p>
            <p><i class="fas fa-clock"></i> Mon-Sat: 8:00 AM - 2:00 PM</p>
          </div>
          <div class="map-container">
            <i class="fas fa-map" style="font-size:2rem;margin-right:10px"></i>
            <span>Google Maps Integration<br><small>Click to open in Maps app</small></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  
  <footer class="footer">
    <div class="container">
      <div class="footer-logo">Prayaag International School</div>
      <p style="opacity:0.9;margin-bottom:15px">Nurturing Tomorrow's Leaders Today</p>
      
      <div class="footer-links">
        <a href="#about">About Us</a>
        <a href="#programs">Academics</a>
        <a href="#facilities">Facilities</a>
        <a href="#admissions">Admissions</a>
        <a href="#contact">Contact</a>
        <a href="#">Privacy Policy</a>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2026 Prayaag International School. All Rights Reserved.</p>
        <p style="margin-top:8px;font-size:0.8rem;opacity:0.8">
          Best School in Panipat | Top CBSE School in Panipat | School Near Samalkha | Best School Near Me
        </p>
      </div>
    </div>
  </footer>

  
  <div class="sticky-cta">
    <a href="tel:+919350748851" class="btn-call">
      <i class="fas fa-phone"></i> Call Now
    </a>
    <a href="https://wa.me/919350748851" class="btn-whatsapp" target="_blank">
      <i class="fab fa-whatsapp"></i> WhatsApp
    </a>
  </div>

  
  <a href="https://wa.me/919350748851" class="floating-wa" target="_blank" aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
  </a>

  
  <script>
    // FAQ Toggle
    document.querySelectorAll('.faq-question').forEach(question => {
      question.addEventListener('click', () => {
        const item = question.parentElement;
        item.classList.toggle('active');
      });
    });

    // Form submission enhancement
    document.querySelector('form')?.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = this.querySelector('.btn-submit');
      const originalText = btn.innerHTML;
      
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
      btn.disabled = true;
      
      // Simulate API call
      setTimeout(() => {
        alert('✅ Thank you! Your request has been received. We\'ll contact you shortly.');
        this.reset();
        btn.innerHTML = originalText;
        btn.disabled = false;
      }, 1500);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
          target.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
      });
    });

    // Intersection Observer for subtle animations
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, {threshold: 0.1});

    document.querySelectorAll('.card, .carousel-item, .testimonial-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      observer.observe(el);
    });
  </script>
PISPHTML,
            ],
        ];
    }
}