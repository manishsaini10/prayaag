<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Team Members / Faculty Grid.
 * Displays photo cards with designation, bio, hover social overlay.
 */
class TeamMemberWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-team';
    }

    public function label(): string
    {
        return 'Team / Faculty Grid';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Mentors & Educators',
            'heading' => 'Meet Our Dedicated Faculty',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Our team of experienced educators guiding young minds.',
            'members' => [
                [
                    'name'        => 'Dr. Rajesh Sharma',
                    'designation' => 'Principal & Academic Director',
                    'meta'        => 'Ph.D. in Education, 22+ Yrs Exp.',
                    'image'       => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80',
                    'bio'         => 'Lorem ipsum dolor sit amet, inspiring students through innovation and excellence in pedagogy.',
                ],
                [
                    'name'        => 'Mrs. Sunita Verma',
                    'designation' => 'Headmistress (Primary Wing)',
                    'meta'        => 'M.A., B.Ed., 16+ Yrs Exp.',
                    'image'       => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
                    'bio'         => 'Dedicated to nurturing early childhood learning with love, care and modern teaching methods.',
                ],
                [
                    'name'        => 'Mr. Vikramaditya Singh',
                    'designation' => 'Senior Physics Faculty',
                    'meta'        => 'M.Sc. Physics (Gold Medalist)',
                    'image'       => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                    'bio'         => 'Passionate physics mentor mentoring top JEE & NEET rankers for over a decade.',
                ],
                [
                    'name'        => 'Ms. Ananya Deshmukh',
                    'designation' => 'Head of Sports & Fitness',
                    'meta'        => 'National Athletic Coach',
                    'image'       => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&auto=format&fit=crop&q=80',
                    'bio'         => 'Championing holistic sports development, sportsmanship and physical fitness on campus.',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $cardsHtml = '';
        $members = (array) $this->setting($settings, 'members', []);

        foreach ($members as $m) {
            $name        = $this->e($m['name'] ?? 'Faculty Name');
            $desig       = $this->e($m['designation'] ?? 'Designation');
            $meta        = $this->e($m['meta'] ?? '');
            $img         = $this->e($m['image'] ?? 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400');
            $bio         = $this->e($m['bio'] ?? '');

            $cardsHtml .= <<<HTML
            <div class="ek-team-card">
                <div class="ek-team-img-wrap">
                    <img src="{$img}" alt="{$name}" class="ek-team-img" loading="lazy">
                    <div class="ek-team-overlay">
                        <span class="ek-team-tag">{$meta}</span>
                    </div>
                </div>
                <div class="ek-team-content">
                    <h3 class="ek-team-name">{$name}</h3>
                    <div class="ek-team-role">{$desig}</div>
                    <p class="ek-team-bio">{$bio}</p>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; max-width: 1140px; margin: 30px auto 0; padding: 0 16px; }
        .ek-team-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: all .3s ease; display: flex; flex-direction: column; }
        .ek-team-card:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(11,37,69,.12); border-color: #c79a3b; }
        .ek-team-img-wrap { position: relative; height: 260px; overflow: hidden; background: #f1f5f9; }
        .ek-team-img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .ek-team-card:hover .ek-team-img { transform: scale(1.08); }
        .ek-team-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(11,37,69,.85) 0%, transparent 60%); display: flex; align-items: flex-end; padding: 16px; opacity: 0; transition: opacity .3s ease; }
        .ek-team-card:hover .ek-team-overlay { opacity: 1; }
        .ek-team-tag { background: #c79a3b; color: #0b2545; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; }
        .ek-team-content { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .ek-team-name { font-size: 18px; font-weight: 700; color: #0b2545; margin: 0 0 4px; }
        .ek-team-role { font-size: 13px; font-weight: 600; color: #c79a3b; margin-bottom: 10px; }
        .ek-team-bio { font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.55; }
        </style>

        <section class="ek-team-sec">
            {$head}
            <div class="ek-team-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
