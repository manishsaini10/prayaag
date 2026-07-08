<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Year-by-year achievements timeline. Defaults carry the existing home-page
 * milestones (2016–2023).
 */
class AchievementsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'achievements';
    }

    public function label(): string
    {
        return 'Achievements Timeline';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Our Achievements',
            'heading' => 'Milestones of Our Journey',
            'sub'     => 'Every day is a stepping stone — here are some of the finest steps we have taken.',
            'rows'    => [
                ['year' => '2016', 'items' => ['Laid the foundation stone of the school']],
                ['year' => '2019', 'items' => ['District Level Karate Competition', 'Wrestling Competition (Block level)', 'Capacity Building Programme By CBSE', 'Annual Function – Let Me Fly']],
                ['year' => '2020', 'items' => ['Vidya Mandir Quest – Biggest National Level Quiz', 'British Council International School Award', 'Go Green Initiative']],
                ['year' => '2021', 'items' => ['National Level Karate Championship', 'Building Resilience – A virtue in Covid Times', 'Dhammika KAT Cup Championship', 'Sports Tournament – District Level', 'Faculty / Staff Sports Tournament', 'State Level Painting Competition']],
                ['year' => '2022', 'items' => ['Celebration of World Environment Day', 'Excursions – Explore by Yourself', 'Fireless Cooking – Experiential Learning', 'Stellar Board Results (2021-22)', 'SOF Olympiad Results', 'Christmas Carnival & Baisakhi Mela']],
                ['year' => '2023', 'items' => ['Educational Trip to Top Ranked University', 'Participated in Rahgiri', 'District Senior Wushu Championship', 'National India Open Shooting Championship', 'District Swimming Championship', 'Nukkad Natak, Run for Victory, Veer Bal Diwas', 'Geeta Mahotsav & Career Counselling']],
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

        $rows = '';
        foreach ((array) $this->setting($settings, 'rows', []) as $row) {
            $year = $this->e($row['year'] ?? '');
            $items = '';
            foreach ((array) ($row['items'] ?? []) as $it) {
                $items .= '<li>' . $this->e($it) . '</li>';
            }
            $rows .= '<div class="tl-row" data-reveal><div class="tl-year">' . $year . '</div>'
                . '<div class="tl-body"><ul>' . $items . '</ul></div></div>';
        }

        return $head . '<div class="timeline">' . $rows . '</div>';
    }
}
