<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Unfold Expandable Content.
 * Expandable long text box with bottom fade overlay and "Read More" button.
 */
class UnfoldContentWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-unfold-content';
    }

    public function label(): string
    {
        return 'Unfold Content Box';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Founder’s Vision & Philosophy',
            'heading' => 'A Legacy of Educational Transformation',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Founded with a vision to nurture global citizens, Prayaag International School combines rigorous academic standards with holistic co-curricular development. Our 10-acre green campus features smart digital classrooms, advanced science and robotics labs, Olympic-standard sports grounds, and dedicated performing arts auditoriums. We believe every child is born with unique potential. Our customized learning pathways ensure individual mentoring, emotional well-being, and competitive excellence.',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $content = $this->e($this->setting($settings, 'content'));
        $ufId    = 'ek-uf-' . uniqid();

        return <<<HTML
        <style>
        .ek-uf-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 32px; max-width: 850px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.05); position: relative; }
        .ek-uf-body { position: relative; max-height: 120px; overflow: hidden; transition: max-height .5s ease; font-size: 15px; color: #475569; line-height: 1.7; }
        .ek-uf-fade { position: absolute; bottom: 0; left: 0; right: 0; height: 70px; background: linear-gradient(to top, #ffffff 10%, transparent 100%); pointer-events: none; transition: opacity .3s; }
        .ek-uf-card.expanded .ek-uf-body { max-height: 800px; }
        .ek-uf-card.expanded .ek-uf-fade { opacity: 0; }
        .ek-uf-btn-wrap { text-align: center; margin-top: 16px; }
        .ek-uf-btn { background: #0b2545; color: #ffffff; border: none; font-size: 13px; font-weight: 700; padding: 10px 24px; border-radius: 999px; cursor: pointer; transition: background .2s; font-family: inherit; }
        .ek-uf-btn:hover { background: #c79a3b; color: #0b2545; }
        </style>

        <section class="ek-uf-sec">
            {$head}
            <div class="ek-uf-card" id="{$ufId}">
                <div class="ek-uf-body">
                    {$content}
                    <div class="ek-uf-fade"></div>
                </div>
                <div class="ek-uf-btn-wrap">
                    <button type="button" class="ek-uf-btn" onclick="
                        var card = document.getElementById('{$ufId}');
                        card.classList.toggle('expanded');
                        this.textContent = card.classList.contains('expanded') ? 'Show Less ▲' : 'Read Full Story ▼';
                    ">Read Full Story ▼</button>
                </div>
            </div>
        </section>
        HTML;
    }
}
