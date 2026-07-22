<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Subject Access Request — Prayaag School</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top left, #f8fafc, #f1f5f9);
        }
    </style>
</head>
<body class="min-h-screen grid place-items-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Privacy Portal</h2>
            <p class="text-xs text-slate-500 mt-2">Submit a Data Privacy or Deletion Request (GDPR / DPDP Act)</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 text-sm rounded-2xl border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('privacy.submit') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Your Registered Email</label>
                <input type="email" name="email" id="email" required placeholder="email@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Request Type</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="radio" name="request_type" value="export" checked class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-sm text-slate-900 mt-2">Export PII</span>
                        <span class="text-[10px] text-slate-500 mt-1">Download personal data logs.</span>
                    </label>
                    <label class="relative flex flex-col p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="radio" name="request_type" value="delete" class="absolute top-4 right-4 text-red-600 focus:ring-red-500">
                        <span class="font-bold text-sm text-slate-900 mt-2">Anonymize</span>
                        <span class="text-[10px] text-slate-500 mt-1">Anonymize/erase all PII records.</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 text-white font-semibold rounded-2xl shadow-lg hover:bg-slate-800 active:scale-[0.98] transition-all">
                Submit Request
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Prayaag School. All rights reserved.
        </div>
    </div>
</body>
</html>
