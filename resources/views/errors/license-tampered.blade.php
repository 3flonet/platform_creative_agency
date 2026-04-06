<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Integrity Breach - 3FLO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .glitch-text { text-shadow: 2px 0 #7c3aed, -2px 0 #f43f5e; }
    </style>
</head>
<body class="bg-slate-950 flex items-center justify-center min-h-screen p-6 overflow-hidden">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_50%,#7c3aed_0%,transparent_50%)]"></div>
    </div>

    <div class="max-w-md w-full relative">
        <div class="bg-slate-900/50 backdrop-blur-xl border border-red-500/20 rounded-[32px] p-12 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-red-600/10 rounded-full blur-3xl"></div>
            
            <div class="mb-8 flex justify-center">
                <div class="w-20 h-20 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center justify-center text-red-500 animate-pulse">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-white mb-4 glitch-text uppercase tracking-tighter">Integrity Breach</h1>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">
                Sistem mendeteksi adanya manipulasi data lisensi pada database lokal. Demi keamanan data, akses ke dashboard telah dikunci secara otomatis.
            </p>

            <div class="space-y-4">
                <a href="/admin/license/activate" class="block w-full py-4 px-6 bg-red-600 hover:bg-red-500 text-white rounded-2xl font-bold transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-red-600/20">
                    Re-verify Identity
                </a>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Error Code: ERR_SYS_TAMPERED</p>
            </div>
        </div>
    </div>
</body>
</html>
