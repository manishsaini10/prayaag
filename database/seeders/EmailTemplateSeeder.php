<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'template_key' => 'job_application_received',
                'module' => 'careers',
                'subject' => 'Application Received: {{job_title}} at Prayaag International School',
                'body_html' => '<h2>Dear {{candidate_name}},</h2><p>Thank you for applying for the position of <strong>{{job_title}}</strong> at Prayaag International School. We have successfully received your application.</p><p>Our HR team will review your application and contact you if your profile matches our requirements.</p><p>Warm regards,<br>Prayaag HR Team</p>',
                'available_placeholders' => ['candidate_name', 'job_title'],
            ],
            [
                'template_key' => 'job_application_admin_alert',
                'module' => 'careers',
                'subject' => 'New Job Application: {{candidate_name}} for {{job_title}}',
                'body_html' => '<h2>New Job Application Received</h2><p><strong>Candidate Name:</strong> {{candidate_name}}</p><p><strong>Email:</strong> {{candidate_email}}</p><p><strong>Position:</strong> {{job_title}}</p><p><a href="{{admin_application_url}}">View Application in Admin Panel</a></p>',
                'available_placeholders' => ['candidate_name', 'candidate_email', 'job_title', 'admin_application_url'],
            ],
            [
                'template_key' => 'job_application_status_changed',
                'module' => 'careers',
                'subject' => 'Update on your application for {{job_title}}',
                'body_html' => '<h2>Dear {{candidate_name}},</h2><p>The status of your application for <strong>{{job_title}}</strong> has been updated to: <strong>{{status}}</strong>.</p><p>Thank you for your interest in Prayaag International School.</p>',
                'available_placeholders' => ['candidate_name', 'job_title', 'status'],
            ],
            [
                'template_key' => 'enquiry_auto_reply',
                'module' => 'enquiry',
                'subject' => 'Thank you for reaching out to Prayaag International School',
                'body_html' => '<h2>Dear {{name}},</h2><p>Thank you for submitting your enquiry. Our admissions / support office has received your message and will get back to you shortly.</p><p><strong>Your Message:</strong></p><blockquote>{{message}}</blockquote><p>Best regards,<br>Prayaag Admissions Team</p>',
                'available_placeholders' => ['name', 'message'],
            ],
            [
                'template_key' => 'enquiry_admin_alert',
                'module' => 'enquiry',
                'subject' => 'New Web Enquiry: {{name}} ({{type}})',
                'body_html' => '<h2>New Web Enquiry Submitted</h2><p><strong>Name:</strong> {{name}}</p><p><strong>Email:</strong> {{email}}</p><p><strong>Phone:</strong> {{phone}}</p><p><strong>Message:</strong> {{message}}</p>',
                'available_placeholders' => ['name', 'email', 'phone', 'message', 'type'],
            ],
            [
                'template_key' => 'newsletter_subscribe_confirm',
                'module' => 'newsletter',
                'subject' => 'Please confirm your newsletter subscription',
                'body_html' => '<h2>Welcome to Prayaag Newsletter!</h2><p>Please click the button below to confirm your subscription to our updates and newsletters.</p><p><a href="{{confirm_url}}" style="display:inline-block;padding:10px 20px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;">Confirm Subscription</a></p>',
                'available_placeholders' => ['confirm_url'],
            ],
            [
                'template_key' => 'newsletter_campaign',
                'module' => 'newsletter',
                'subject' => '{{campaign_subject}}',
                'body_html' => '{{campaign_body}}',
                'available_placeholders' => ['subscriber_name', 'campaign_subject', 'campaign_body'],
            ],
            [
                'template_key' => 'video_testimonial_submitted_confirmation',
                'module' => 'video_testimonials',
                'subject' => 'Thank you for submitting your video testimonial!',
                'body_html' => '<h2>Dear {{submitter_name}},</h2><p>Thank you for sharing your experience with Prayaag International School. Your video testimonial has been received and is currently under review by our moderation team.</p>',
                'available_placeholders' => ['submitter_name', 'video_title'],
            ],
            [
                'template_key' => 'video_testimonial_admin_moderation_alert',
                'module' => 'video_testimonials',
                'subject' => 'New Video Testimonial Pending Review: {{title}}',
                'body_html' => '<h2>New Video Testimonial Submitted</h2><p><strong>Title:</strong> {{title}}</p><p><strong>Submitted By:</strong> {{submitter_name}}</p><p><a href="{{admin_review_url}}">Review Testimonial in Admin Console</a></p>',
                'available_placeholders' => ['title', 'submitter_name', 'admin_review_url'],
            ],
            [
                'template_key' => 'mess_menu_weekly_digest',
                'module' => 'mess_menu',
                'subject' => 'Weekly Dining Menu — Prayaag International School',
                'body_html' => '<h2>Weekly Mess Menu: {{menu_title}}</h2><p>Here is the dining menu for this week:</p>{{menu_items_html}}<p><a href="{{pdf_link}}">Download PDF Menu</a></p>',
                'available_placeholders' => ['menu_title', 'menu_items_html', 'pdf_link'],
            ],
            [
                'template_key' => 'chatbot_human_escalation_alert',
                'module' => 'chatbot',
                'subject' => 'Chatbot Lead Escalation: {{visitor_name}} requires human support',
                'body_html' => '<h2>Human Takeover Requested</h2><p>A visitor requires human assistance on the website chatbot.</p><p><strong>Name:</strong> {{visitor_name}}</p><p><strong>Email/Phone:</strong> {{visitor_contact}}</p><p><strong>Summary:</strong> {{summary}}</p>',
                'available_placeholders' => ['visitor_name', 'visitor_contact', 'summary'],
            ],
        ];

        foreach ($templates as $tmpl) {
            EmailTemplate::updateOrCreate(
                ['template_key' => $tmpl['template_key']],
                $tmpl
            );
        }
    }
}
