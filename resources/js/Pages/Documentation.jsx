import { Head } from '@inertiajs/react';
import React, { useEffect } from 'react';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Documentation({ content, settings = {} }) {
    useEffect(() => {
        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
        return () => lenis.destroy();
    }, []);

    const getSetting = (key, fallback) => {
        if (!settings || typeof settings !== 'object') return fallback;
        const val = settings[key] || fallback;
        if (typeof val === 'string' && val.includes('/') && !val.startsWith('http')) {
            return `/storage/${val}`;
        }
        return val;
    };

    const siteName = getSetting('site_name', '3FLO');

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`Documentation - ${siteName}`} />

            <Navbar settings={settings} />

            <main className="pt-32">
                <section className="px-6 md:px-32 py-20">
                    <div className="max-w-4xl mx-auto">
                        <header className="mb-16">
                            <h2 className="text-xs uppercase tracking-[1em] mb-4 text-red-600 font-black">Archive</h2>
                            <h1 className="text-4xl md:text-6xl font-black uppercase tracking-tighter italic leading-none">
                                Documentation <br/> & Guidelines
                            </h1>
                            <div className="h-1 w-20 bg-red-600 mt-10"></div>
                        </header>

                        <article 
                            className="prose prose-invert prose-red max-w-none 
                                       prose-headings:uppercase prose-headings:tracking-tighter prose-headings:font-black prose-headings:italic
                                       prose-h1:text-5xl prose-h2:text-3xl prose-h2:border-b prose-h2:border-white/10 prose-h2:pb-4 prose-h2:mt-16
                                       prose-p:text-white/60 prose-p:leading-relaxed prose-p:text-lg
                                       prose-li:text-white/60 prose-strong:text-white prose-strong:font-black
                                       prose-hr:border-white/5 prose-table:border prose-table:border-white/10
                                       prose-th:text-red-600 prose-th:uppercase prose-th:tracking-widest prose-th:text-[10px]"
                            dangerouslySetInnerHTML={{ __html: content }}
                        />
                    </div>
                </section>
            </main>

            <Footer settings={settings} getSetting={getSetting} />
        </div>
    );
}
