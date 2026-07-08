<?php

namespace App\Enums\Popup;

enum DisplayCondition: string
{
    case EntireWebsite = 'entire_website';
    case Homepage = 'homepage';
    case LandingPages = 'landing_pages';
    case SpecificPages = 'specific_pages';
    case SpecificPosts = 'specific_posts';
    case Categories = 'categories';
    case Tags = 'tags';
    case Admissions = 'admissions';
    case Blog = 'blog';
    case Gallery = 'gallery';
    case Events = 'events';
    case Careers = 'careers';
    case Contact = 'contact';
    case NotFound = '404';
    case SearchPage = 'search_page';
    case Login = 'login';
    case Register = 'register';
    case Dashboard = 'dashboard';
    case Profile = 'profile';
    case Checkout = 'checkout';
    case ThankYou = 'thank_you';
    case CustomUrl = 'custom_url';
    case WildcardUrl = 'wildcard_url';
    case Regex = 'regex';

    public function label(): string { return str_replace('_', ' ', ucwords($this->value, '_')); }
}

enum FrequencyType: string
{
    case EveryVisit = 'every_visit';
    case OncePerSession = 'once_per_session';
    case OncePerDay = 'once_per_day';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case OnceOnly = 'once_only';
    case AfterXDays = 'after_x_days';
    case NeverAgain = 'never_again';
    case CookieBased = 'cookie_based';
    case DatabaseBased = 'database_based';

    public function label(): string { return str_replace('_', ' ', ucwords($this->value, '_')); }
}

enum TargetingType: string
{
    case Guests = 'guests';
    case LoggedIn = 'logged_in';
    case Students = 'students';
    case Parents = 'parents';
    case Teachers = 'teachers';
    case Staff = 'staff';
    case Admin = 'admin';
    case SpecificRoles = 'specific_roles';
    case FirstTime = 'first_time';
    case Returning = 'returning';
    case NewUsers = 'new_users';
    case ExistingUsers = 'existing_users';
    case Country = 'country';
    case State = 'state';
    case City = 'city';
    case Language = 'language';
    case Timezone = 'timezone';
    case ReferralSource = 'referral_source';
    case UtmParameters = 'utm_parameters';
    case CampaignSource = 'campaign_source';
    case Browser = 'browser';
    case Os = 'os';
    case Desktop = 'desktop';
    case Tablet = 'tablet';
    case Mobile = 'mobile';
    case ScreenWidth = 'screen_width';
    case ScreenHeight = 'screen_height';
    case Cookies = 'cookies';
    case Session = 'session';
    case LoginStatus = 'login_status';
    case CustomConditions = 'custom_conditions';

    public function label(): string { return str_replace('_', ' ', ucwords($this->value, '_')); }
}

enum AnimationType: string
{
    case Fade = 'fade';
    case Zoom = 'zoom';
    case Slide = 'slide';
    case Bounce = 'bounce';
    case Rotate = 'rotate';
    case Elastic = 'elastic';
    case Scale = 'scale';
    case Flip = 'flip';
    case Pulse = 'pulse';
    case Shake = 'shake';
    case Custom = 'custom';

    public function label(): string { return ucfirst($this->value); }
}

enum PopupPosition: string
{
    case CenterCenter = 'center-center';
    case TopLeft = 'top-left';
    case TopCenter = 'top-center';
    case TopRight = 'top-right';
    case BottomLeft = 'bottom-left';
    case BottomCenter = 'bottom-center';
    case BottomRight = 'bottom-right';
    case LeftCenter = 'left-center';
    case RightCenter = 'right-center';

    public function label(): string { return str_replace('-', ' ', ucwords($this->value, '-')); }
}

enum AbTestStatus: string
{
    case Draft = 'draft';
    case Running = 'running';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string { return ucfirst($this->value); }
}

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Converted = 'converted';
    case Lost = 'lost';
    case Archived = 'archived';

    public function label(): string { return ucfirst($this->value); }
}

enum IntegrationType: string
{
    case Mailchimp = 'mailchimp';
    case Brevo = 'brevo';
    case Slack = 'slack';
    case Telegram = 'telegram';
    case Discord = 'discord';
    case WhatsApp = 'whatsapp';
    case Zapier = 'zapier';
    case GoogleSheets = 'google_sheets';
    case Webhook = 'webhook';
    case RestApi = 'rest_api';
    case Crm = 'crm';

    public function label(): string { return str_replace('_', ' ', ucwords($this->value, '_')); }
}
