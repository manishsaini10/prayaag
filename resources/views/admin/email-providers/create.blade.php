@extends('admin.layout')

@section('title', 'Add Email Provider')
@section('subtitle', 'Configure a new live email sending provider')

@section('content')
<div class="max-w-3xl mx-auto bg-slate-900/90 rounded-2xl p-8 border border-slate-700/80 shadow-md" x-data="{ type: 'hostinger' }">
    <form action="{{ route('admin.email-providers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Select Email Provider Type</label>
            <select name="provider_key" x-model="type" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-3 text-sm focus:ring-indigo-500 font-medium">
                <option value="hostinger">Hostinger / cPanel Webmail (Recommended for Hostinger Hosting)</option>
                <option value="gmail">Google Gmail / Workspace (App Password)</option>
                <option value="zoho">Zoho Mail (SMTP)</option>
                <option value="smtp">Custom SMTP Server (Any Hosting / Hostinger / Mailtrap)</option>
                <option value="brevo">Brevo (Sendinblue HTTP API)</option>
                <option value="elastic_email">Elastic Email (HTTP API)</option>
                <option value="mailjet">Mailjet (HTTP API)</option>
                <option value="ses">Amazon SES (AWS SMTP)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Provider Label / Name</label>
            <input type="text" name="label" required placeholder="e.g. Hostinger Primary Mail or Gmail Official" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-3 text-sm">
        </div>

        <!-- 1. Gmail Fields & Requirements -->
        <div x-show="type === 'gmail'" class="space-y-4 pt-4 border-t border-slate-800" x-cloak>
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for Google Gmail / Workspace:</p>
                <ol class="list-decimal list-inside space-y-1 text-slate-300">
                    <li>Go to your <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-indigo-400 underline font-bold">Google Account App Passwords</a>.</li>
                    <li>Ensure 2-Step Verification is turned <strong>ON</strong> in Security settings.</li>
                    <li>Generate a new App Password (e.g. name it <code>Prayaag CMS</code>) and copy the 16-character code below.</li>
                </ol>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gmail Email Address</label>
                    <input type="email" name="credentials[username]" placeholder="yourname@gmail.com" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Google App Password (16 chars)</label>
                    <input type="password" name="credentials[password]" placeholder="abcd efgh ijkl mnop" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Email</label>
                    <input type="email" name="credentials[from_email]" placeholder="yourname@gmail.com" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
        </div>

        <!-- 2. Hostinger / cPanel Fields & Requirements -->
        <div x-show="type === 'hostinger'" class="space-y-4 pt-4 border-t border-slate-800">
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for Hostinger / cPanel Webmail:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-300">
                    <li>Create an Email Account in Hostinger hPanel or cPanel (e.g. <code>info@prayaag.edu.in</code>).</li>
                    <li>Default Host: <code>smtp.hostinger.com</code> (or <code>mail.yourdomain.com</code>).</li>
                    <li>Default SSL Port: <code>465</code> (or <code>587</code> for TLS).</li>
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">SMTP Host</label>
                    <input type="text" name="credentials[host]" value="smtp.hostinger.com" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Port</label>
                    <input type="number" name="credentials[port]" value="465" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Encryption</label>
                    <select name="credentials[encryption]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                        <option value="ssl">SSL (Port 465)</option>
                        <option value="tls">TLS (Port 587)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Webmail Address (Username)</label>
                    <input type="email" name="credentials[username]" placeholder="info@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Webmail Password</label>
                    <input type="password" name="credentials[password]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">From Email</label>
                <input type="email" name="credentials[from_email]" placeholder="info@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
            </div>
        </div>

        <!-- 3. Zoho Fields & Requirements -->
        <div x-show="type === 'zoho'" class="space-y-4 pt-4 border-t border-slate-800" x-cloak>
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for Zoho Mail:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-300">
                    <li>Create an App Password in <a href="https://accounts.zoho.in/#security/security-apppass" target="_blank" class="text-indigo-400 underline font-bold">Zoho Accounts Security</a> if 2FA is active.</li>
                    <li>Select your Zoho Region (India: <code>smtp.zoho.in</code>, Global: <code>smtp.zoho.com</code>).</li>
                </ul>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Zoho Region</label>
                <select name="credentials[region]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                    <option value="in">India (smtp.zoho.in)</option>
                    <option value="global">Global (smtp.zoho.com)</option>
                    <option value="eu">Europe (smtp.zoho.eu)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Zoho Email Address</label>
                    <input type="email" name="credentials[username]" placeholder="admin@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Zoho Password / App Password</label>
                    <input type="password" name="credentials[password]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Email</label>
                    <input type="email" name="credentials[from_email]" placeholder="admin@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
        </div>

        <!-- 4. Generic Custom SMTP Fields & Requirements -->
        <div x-show="type === 'smtp'" class="space-y-4 pt-4 border-t border-slate-800" x-cloak>
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for Custom SMTP:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-300">
                    <li>Obtain Host, Port (587 TLS or 465 SSL), Username, and Password from your Mail Server or Hosting Provider.</li>
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Host</label>
                    <input type="text" name="credentials[host]" placeholder="smtp.mailtrap.io" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Port</label>
                    <input type="number" name="credentials[port]" value="587" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Encryption</label>
                    <select name="credentials[encryption]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username</label>
                    <input type="text" name="credentials[username]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input type="password" name="credentials[password]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">From Email</label>
                <input type="email" name="credentials[from_email]" placeholder="noreply@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
            </div>
        </div>

        <!-- 5. API Provider Fields & Requirements (Brevo / Elastic / Mailjet) -->
        <div x-show="['brevo', 'elastic_email', 'mailjet'].includes(type)" class="space-y-4 pt-4 border-t border-slate-800" x-cloak>
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for API Providers:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-300">
                    <li>Copy your API Key from your provider dashboard (Brevo, Elastic Email, or Mailjet).</li>
                    <li>Ensure your <strong>From Email</strong> address is verified in your API provider account.</li>
                </ul>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">API Key</label>
                <input type="password" name="credentials[api_key]" placeholder="v3 / v4 API Key" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
            </div>
            <div x-show="type === 'mailjet'">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Secret Key (Mailjet only)</label>
                <input type="password" name="credentials[secret_key]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Email (Verified)</label>
                    <input type="email" name="credentials[from_email]" placeholder="info@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
        </div>

        <!-- 6. Amazon SES Fields & Requirements -->
        <div x-show="type === 'ses'" class="space-y-4 pt-4 border-t border-slate-800" x-cloak>
            <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs leading-relaxed space-y-1.5">
                <p class="font-bold text-sm text-indigo-300">📋 Requirements for Amazon SES:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-300">
                    <li>Create SES SMTP Credentials in AWS Console > Amazon SES > Account Dashboard > Create SMTP Credentials.</li>
                    <li>Ensure your From Email or Domain is verified in AWS SES.</li>
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">AWS Region</label>
                    <input type="text" name="credentials[region]" value="us-east-1" placeholder="us-east-1 or ap-south-1" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">SES SMTP Username</label>
                    <input type="text" name="credentials[username]" placeholder="AKIA..." class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">SES SMTP Password</label>
                    <input type="password" name="credentials[password]" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Name</label>
                    <input type="text" name="credentials[from_name]" value="Prayaag International School" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">From Email (Verified in AWS SES)</label>
                <input type="email" name="credentials[from_email]" placeholder="info@prayaag.edu.in" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-800">
            <a href="{{ route('admin.email-providers.index') }}" class="btn">Cancel</a>
            <button type="submit" class="btn primary">Save Provider</button>
        </div>
    </form>
</div>
@endsection
