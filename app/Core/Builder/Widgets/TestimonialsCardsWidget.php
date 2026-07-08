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

        $items = $this->setting($settings, 'items', []);
        $cards = '';
        $i = 0;
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
