<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Parent testimonial cards. Defaults carry the existing home-page testimonials.
 */
class TestimonialsCardsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'parent-testimonials';
    }

    public function label(): string
    {
        return 'Testimonials (cards)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'In Their Words',
            'heading' => 'Parents Testimonials',
            'items'   => [
                ['quote' => 'Choosing Prayaag International School for our child has been one of the best decisions we made as parents. The school’s caring and supportive staff, excellent infrastructure, and consistent academic achievements make it the top choice in Panipat.', 'name' => 'Mr. Kamal Chuge', 'role' => 'F/O Felix VII H'],
                ['quote' => 'Prayaag International School’s commitment to holistic education is commendable. The school’s focus on sports, arts, and cultural activities, along with academics, ensures that children receive a well-balanced education that prepares them for life.', 'name' => 'Ms. Rajni', 'role' => 'M/O Diksha IX Z'],
                ['quote' => 'I can confidently say that Prayaag International School is the best school in Panipat for promoting a love for learning. The teachers create an engaging and stimulating environment, fostering a passion for knowledge among the students.', 'name' => 'Ms. Neelam', 'role' => 'M/O Bhumi IX H'],
                ['quote' => 'Prayaag International School has a fantastic support system for students with different abilities. The inclusive approach and personalized attention given to each child make it a top school in Panipat for catering to diverse learning needs.', 'name' => 'Mr. Pratap Singh', 'role' => 'F/O Devika XI H'],
                ['quote' => 'We are extremely satisfied with our decision to enroll our child in Prayaag International School. The school’s strong focus on values, ethics, and discipline, combined with excellent academic programs, makes it the best choice in Panipat.', 'name' => 'Mr. Vishal Garg', 'role' => 'F/O Shalok Garg X P'],
                ['quote' => 'Prayaag International School provides a well-rounded education that prepares children for the future. The school’s commitment to innovation, technology integration, and experiential learning makes it stand out among the top schools in Panipat.', 'name' => 'Ms. Pinki', 'role' => 'M/O Shelly IX H'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead($this->setting($settings, 'eyebrow'), $this->setting($settings, 'heading'));

        // Query database first for published testimonials
        $style = \App\Core\Settings\Settings::get('testimonial_display_style', 'slider');
        $limit = (int) \App\Core\Settings\Settings::get('testimonial_display_limit', 6);
        $autoplay = (int) \App\Core\Settings\Settings::get('testimonial_slider_autoplay_interval', 5);

        $dbItems = \App\Models\Testimonial::published()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $i = 0;

        if ($dbItems->isNotEmpty()) {
            if ($style === 'slider') {
                $cards = '';
                foreach ($dbItems as $t) {
                    $quote = $this->e($t->testimonial ?? '');
                    $name  = $this->e($t->name ?? '');
                    $role  = $this->e($t->role ?? '');
                    $image = $t->image;

                    if ($image) {
                        $avHtml = '<img src="' . asset($image) . '" class="av" style="object-fit:cover">';
                    } else {
                        $av = $this->e($this->initials($t->name ?? ''));
                        $avHtml = '<div class="av">' . $av . '</div>';
                    }

                    $rating = max(0, min(5, (int) ($t->rating ?? 5)));
                    $stars = '<div class="tcard-stars" style="color:#f59e0b;font-size:11px;margin-bottom:8px;letter-spacing:1px">' 
                        . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</div>';

                    $cards .= '<div class="tcard-slide" style="flex:0 0 100%; max-width:100%; box-sizing:border-box; padding: 0 10px;">'
                        . '<div class="tcard" style="height:100%; display:flex; flex-direction:column; justify-content:space-between;">'
                        . '<div>'
                        . $stars
                        . '<div class="qmark">&ldquo;</div><p>' . $quote . '</p>'
                        . '</div>'
                        . '<div class="who">' . $avHtml
                        . '<div><b>' . $name . '</b><small>' . $role . '</small></div></div></div></div>';
                }

                $count = $dbItems->count();

                return $head . '
                <style>
                .tcard-slider-container {
                    position: relative;
                    overflow: hidden;
                    width: 100%;
                    padding: 16px 0;
                }
                .tcard-slider-track {
                    display: flex;
                    gap: 24px;
                    overflow-x: auto;
                    scroll-behavior: smooth;
                    width: 100%;
                    scrollbar-width: none;  /* Firefox */
                    -ms-overflow-style: none;  /* IE and Edge */
                }
                .tcard-slider-track::-webkit-scrollbar {
                    display: none;  /* Chrome, Safari, Opera */
                }
                .tcard-slide {
                    flex: 0 0 100%;
                    max-width: 100%;
                    box-sizing: border-box;
                    padding: 0 10px;
                }
                @media (min-width: 768px) {
                    .tcard-slide {
                        flex: 0 0 calc(50% - 12px) !important;
                        max-width: calc(50% - 12px) !important;
                    }
                }
                @media (min-width: 1024px) {
                    .tcard-slide {
                        flex: 0 0 calc(33.333% - 16px) !important;
                        max-width: calc(33.333% - 16px) !important;
                    }
                }
                </style>
                <div x-data="{
                    next() {
                        const track = this.$refs.track;
                        if (!track) return;
                        const cardWidth = track.firstElementChild ? (track.firstElementChild.getBoundingClientRect().width + 24) : 300;
                        track.scrollBy({ left: cardWidth, behavior: \'smooth\' });
                        if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                            track.scrollTo({ left: 0, behavior: \'smooth\' });
                        }
                    },
                    prev() {
                        const track = this.$refs.track;
                        if (!track) return;
                        const cardWidth = track.firstElementChild ? (track.firstElementChild.getBoundingClientRect().width + 24) : 300;
                        track.scrollBy({ left: -cardWidth, behavior: \'smooth\' });
                        if (track.scrollLeft <= 10) {
                            track.scrollTo({ left: track.scrollWidth, behavior: \'smooth\' });
                        }
                    },
                    init() {
                        setInterval(() => this.next(), ' . ($autoplay * 1000) . ');
                    }
                }" class="tcard-slider-container">
                    <div class="tcard-slider-track" x-ref="track">
                        ' . $cards . '
                    </div>
                    
                    <div style="display: flex; justify-content: center; gap: 12px; align-items: center; margin-top: 24px;">
                        <button @click="prev()" style="cursor: pointer; padding: 6px; background: #fff; border: 1px solid var(--line); border-radius: 50%; font-size: 14px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: var(--navy); font-weight: bold;">&larr;</button>
                        <button @click="next()" style="cursor: pointer; padding: 6px; background: #fff; border: 1px solid var(--line); border-radius: 50%; font-size: 14px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: var(--navy); font-weight: bold;">&rarr;</button>
                    </div>
                </div>';
            } elseif ($style === 'list') {
                $cards = '';
                foreach ($dbItems as $t) {
                    $quote = $this->e($t->testimonial ?? '');
                    $name  = $this->e($t->name ?? '');
                    $role  = $this->e($t->role ?? '');
                    $image = $t->image;

                    if ($image) {
                        $avHtml = '<img src="' . asset($image) . '" class="av" style="object-fit:cover">';
                    } else {
                        $av = $this->e($this->initials($t->name ?? ''));
                        $avHtml = '<div class="av">' . $av . '</div>';
                    }

                    $rating = max(0, min(5, (int) ($t->rating ?? 5)));
                    $stars = '<div class="tcard-stars" style="color:#f59e0b;font-size:11px;margin-bottom:8px;letter-spacing:1px">' 
                        . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</div>';

                    $delay = ($i % 3) + 1;
                    $i++;

                    $cards .= '<div class="tcard" data-reveal data-reveal-delay="' . $delay . '">'
                        . $stars
                        . '<div class="qmark">&ldquo;</div><p>' . $quote . '</p>'
                        . '<div class="who">' . $avHtml
                        . '<div><b>' . $name . '</b><small>' . $role . '</small></div></div></div>';
                }

                return $head . '<div class="tcards" style="grid-template-columns: 1fr; max-width: 800px; margin: 0 auto;">' . $cards . '</div>';
            } else {
                // Default grid layout
                $cards = '';
                foreach ($dbItems as $t) {
                    $quote = $this->e($t->testimonial ?? '');
                    $name  = $this->e($t->name ?? '');
                    $role  = $this->e($t->role ?? '');
                    $image = $t->image;

                    if ($image) {
                        $avHtml = '<img src="' . asset($image) . '" class="av" style="object-fit:cover">';
                    } else {
                        $av = $this->e($this->initials($t->name ?? ''));
                        $avHtml = '<div class="av">' . $av . '</div>';
                    }

                    $rating = max(0, min(5, (int) ($t->rating ?? 5)));
                    $stars = '<div class="tcard-stars" style="color:#f59e0b;font-size:11px;margin-bottom:8px;letter-spacing:1px">' 
                        . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</div>';

                    $delay = ($i % 3) + 1;
                    $i++;

                    $cards .= '<div class="tcard" data-reveal data-reveal-delay="' . $delay . '">'
                        . $stars
                        . '<div class="qmark">&ldquo;</div><p>' . $quote . '</p>'
                        . '<div class="who">' . $avHtml
                        . '<div><b>' . $name . '</b><small>' . $role . '</small></div></div></div>';
                }

                return $head . '<div class="tcards">' . $cards . '</div>';
            }
        } else {
            // Fallback to static settings items if database is empty
            $items = $this->setting($settings, 'items', []);
            $cards = '';
            foreach ((array) $items as $t) {
                $quote = $this->e($t['quote'] ?? '');
                $name  = $this->e($t['name'] ?? '');
                $role  = $this->e($t['role'] ?? '');
                $av    = $this->e($this->initials($t['name'] ?? ''));
                $delay = ($i % 3) + 1;
                $i++;
                $cards .= '<div class="tcard" data-reveal data-reveal-delay="' . $delay . '">'
                    . '<div class="qmark">&ldquo;</div><p>' . $quote . '</p>'
                    . '<div class="who"><div class="av">' . $av . '</div>'
                    . '<div><b>' . $name . '</b><small>' . $role . '</small></div></div></div>';
            }
            return $head . '<div class="tcards">' . $cards . '</div>';
        }
    }
}
