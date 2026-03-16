import { Head, Link } from '@inertiajs/react';
import React, { useEffect } from 'react';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';
import SocialIcons from '@/Components/Content/SocialIcons';
import ServiceMatrix from '@/Components/Content/ServiceMatrix';

export default function Index({ services = [], settings = {} }) {
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
        
        const isPath = typeof val === 'string' && 
                       val.includes('/') && 
                       !val.startsWith('http') && 
                       !val.includes('<') && 
                       !val.includes('>');
        
        if (isPath) {
            return `/storage/${val}`;
        }
        return val;
    };

    const siteName = getSetting('site_name', '3FLO');

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`Services - ${siteName}`} />

            <Navbar settings={settings} />

            <main className="pt-32 pb-40">
                <section className="px-6 md:px-32 mb-20">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">
                            {getSetting('section_2_label', '02. The Matrix')}
                        </h2>
                        <h1 className="text-4xl md:text-[8vw] font-black uppercase tracking-tighter italic mb-12 md:mb-20 leading-none">
                            {getSetting('section_2_title', 'Capabilities')}
                        </h1>
                        <ServiceMatrix services={services} />
                    </div>
                </section>
            </main>

            {/* Footer */}
            <Footer settings={settings} getSetting={getSetting} />
        </div>
    );
}
