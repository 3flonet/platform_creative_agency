import { Head, Link, useForm } from '@inertiajs/react';
import React, { useState, useEffect, useRef } from 'react';
import IntroPreloader from '@/Components/IntroPreloader';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';
import Scene from '@/Components/Experience/Scene';
import ServiceMatrix from '@/Components/Content/ServiceMatrix';
import ProjectGallery from '@/Components/Content/ProjectGallery';
import TeamSection from '@/Components/Content/TeamSection';
import JournalSection from '@/Components/Content/JournalSection';
import SocialIcons from '@/Components/Content/SocialIcons';
import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Variable penanda agar intro hanya jalan 1x per sesi browser
let introHasPlayed = false;

export default function Welcome({ auth = {}, services = [], projects = [], team = [], featuredArticles = [], settings = {} }) {
    // Jika sudah pernah jalan atau ada hash link (deep link), langsung skip loading
    const shouldSkipIntro = introHasPlayed || (typeof window !== 'undefined' && window.location.hash);
    const [loading, setLoading] = useState(!shouldSkipIntro);
    const mainContentRef = useRef(null);

    const { data, setData, post, processing, errors, reset, wasSuccessful } = useForm({
        name: '', email: '', phone: '', subject: '', message: '',
    });

    const getSetting = (key, fallback) => {
        if (!settings || typeof settings !== 'object') return fallback;
        const val = settings[key] || fallback;
        
        const isPath = typeof val === 'string' && 
                       val.includes('/') && 
                       !val.startsWith('http') && 
                       !val.includes('<') && 
                       !val.includes('>');
        
        if (isPath) return `/storage/${val}`;
        return val;
    };

    const handleIntroComplete = () => {
        setLoading(false);
        introHasPlayed = true; // Tandai sudah jalan
    };

    const submitContact = (e) => {
        e.preventDefault();
        post(route('contact.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    useEffect(() => {
        if (loading) return;

        const lenis = new Lenis({
            duration: 1.2,
            smoothWheel: true,
            wheelMultiplier: 1,
            lerp: 0.1,
        });

        lenis.on('scroll', ScrollTrigger.update);
        const tickerFn = (time) => lenis.raf(time * 1000);
        gsap.ticker.add(tickerFn);
        gsap.ticker.lagSmoothing(0);

        const resizeObserver = new ResizeObserver(() => ScrollTrigger.refresh());
        if (mainContentRef.current) resizeObserver.observe(mainContentRef.current);

        const initTimers = [
            setTimeout(() => ScrollTrigger.refresh(), 100),
            setTimeout(() => ScrollTrigger.refresh(), 1000)
        ];

        if (window.location.hash) {
            setTimeout(() => {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    lenis.scrollTo(target, { duration: 1.5 });
                }
            }, 500);
        }

        return () => {
            lenis.destroy();
            gsap.ticker.remove(tickerFn);
            resizeObserver.disconnect();
            initTimers.forEach(t => clearTimeout(t));
        };
    }, [loading]);

    const siteName = getSetting('site_name', '3FLO.');

    return (
        <div className="bg-[#050505] min-h-screen selection:bg-red-600 selection:text-white">
            <Head>
                <title>{`${siteName} | ${getSetting('hero_title_2', 'Creative')} Platform Solutions`}</title>
            </Head>
            
            {loading && (
                <IntroPreloader 
                    onComplete={handleIntroComplete} 
                    sequence={getSetting('intro_sequence', 'Creative,Innovative,Non-stop').split(',')}
                    brand={getSetting('intro_brand', '3FLO.COM')}
                    speed={getSetting('intro_speed', 0.5)}
                    useLogo={getSetting('intro_use_logo') === '1' || getSetting('intro_use_logo') === 1}
                    logoUrl={getSetting('intro_logo') ? getSetting('intro_logo') : null}
                />
            )}
 
            <div className={`transition-opacity duration-1000 ${loading ? 'opacity-0 h-screen overflow-hidden' : 'opacity-100'}`}>
                <Scene settings={settings} isReady={!loading} />

                <div className="relative z-10 text-white">
                    <Navbar settings={settings} />

                    <main id="main-content" ref={mainContentRef} className="w-full">
                        <section id="discovery" className="min-h-screen flex items-center px-6 md:px-32">
                            <div className="max-w-5xl">
                                <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-bold">{getSetting('section_1_label', '01. Discovery')}</h2>
                                <h1 className="hero-title text-[12vw] md:text-[8vw] font-black tracking-tighter leading-[0.85] uppercase mb-4">
                                    {getSetting('hero_title_1', 'Non-stop')}<br />
                                    <span className="text-outline-white italic font-black font-['Poppins']">{getSetting('hero_title_2', 'Creative')}.</span>
                                </h1>
                                <div className="mt-12 md:mt-16 flex items-center gap-4 md:gap-8">
                                    <div className="h-[1px] w-12 md:w-24 bg-red-600 animate-expand-width"></div>
                                    <p className="text-sm md:text-2xl font-light text-white/50 max-w-lg uppercase tracking-[0.2em]">
                                        {getSetting('hero_tagline', 'Where ideas meet infinity.')}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section id="services" className="min-h-screen px-6 md:px-32 py-24 md:py-40">
                            <div className="max-w-7xl mx-auto">
                                <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">{getSetting('section_2_label', '02. The Matrix')}</h2>
                                <h1 className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic mb-12 md:mb-20 whitespace-normal md:whitespace-nowrap leading-none">{getSetting('section_2_title', 'Infinite Services')}</h1>
                                <ServiceMatrix services={services} />
                            </div>
                        </section>

                        <section id="projects" className="min-h-screen px-6 md:px-32 py-24 md:py-40">
                            <div className="max-w-7xl mx-auto">
                                <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">{getSetting('section_3_label', '03. The Works')}</h2>
                                <h1 className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic mb-12 md:mb-20 text-left md:text-right whitespace-normal md:whitespace-nowrap leading-none">{getSetting('section_3_title', 'The Archive')}</h1>
                                <ProjectGallery projects={projects} />
                            </div>
                        </section>

                        <section id="team" className="min-h-screen px-6 md:px-32 py-24 md:py-40">
                            <div className="max-w-7xl mx-auto">
                                <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">{getSetting('section_4_label', '04. The People')}</h2>
                                <h1 className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic mb-12 md:mb-20 whitespace-normal md:whitespace-nowrap leading-none">{getSetting('section_4_title', 'Our Team')}</h1>
                                <TeamSection team={team} />
                            </div>
                        </section>

                        {featuredArticles.length > 0 && (
                            <section id="journal" className="min-h-screen px-6 md:px-32 py-24 md:py-40">
                                <div className="max-w-7xl mx-auto">
                                    <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">{getSetting('journal_label', '05. The Narrative')}</h2>
                                    <div className="flex flex-col md:flex-row justify-between items-start md:items-end gap-8 mb-12 md:mb-20">
                                        <h1 className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic whitespace-normal md:whitespace-nowrap leading-none">{getSetting('journal_title', 'Latest Stories')}</h1>
                                        <Link href={route('journal.index')} className="text-[9px] font-bold uppercase tracking-widest hover:text-red-600 transition-all px-6 py-2.5 border border-white/10 rounded-full">
                                            Enter Journal →
                                        </Link>
                                    </div>
                                    <JournalSection articles={featuredArticles} getSetting={getSetting} />
                                </div>
                            </section>
                        )}

                        <section id="contact" className="min-h-screen px-6 md:px-32 py-24 md:py-40 border-y border-white/5">
                            <div className="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 md:gap-20">
                                <div>
                                    <h2 className="text-[10px] md:text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">{getSetting('section_5_label', '06. Connection')}</h2>
                                    <h1 className="text-4xl md:text-[5vw] font-black uppercase tracking-tighter italic mb-10 leading-none" dangerouslySetInnerHTML={{ __html: getSetting('section_5_title', "Let's <br/>Build <br/>Together.") }}></h1>
                                    
                                    <div className="space-y-8 md:space-y-12 mt-12 md:mt-20">
                                        {getSetting('contact_email') && (
                                            <div>
                                                <h4 className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30 mb-2">Email</h4>
                                                <a href={`mailto:${getSetting('contact_email')}`} className="text-xl md:text-2xl font-light hover:text-red-600 transition-colors uppercase tracking-tight break-all">{getSetting('contact_email')}</a>
                                            </div>
                                        )}
                                        {getSetting('contact_phone') && (
                                            <div>
                                                <h4 className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30 mb-2">Phone</h4>
                                                <a href={`tel:${getSetting('contact_phone')}`} className="text-xl md:text-2xl font-light hover:text-red-600 transition-colors uppercase tracking-tight">{getSetting('contact_phone')}</a>
                                            </div>
                                        )}
                                        {getSetting('contact_address') && (
                                            <div>
                                                <h4 className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30 mb-2">Studio</h4>
                                                <p className="text-xs md:text-sm font-light text-white/60 leading-relaxed uppercase tracking-widest max-w-sm">{getSetting('contact_address')}</p>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div className="bg-white/5 p-6 md:p-10 backdrop-blur-3xl border border-white/5">
                                    <form onSubmit={submitContact} className="space-y-6 md:space-y-8">
                                        <div className="space-y-2">
                                            <label className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30">Your Name</label>
                                            <input type="text" required value={data.name} onChange={e => setData('name', e.target.value)} className="w-full bg-transparent border-b border-white/10 py-3 md:py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs" placeholder="Enter your name" />
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30">Email Address</label>
                                            <input type="email" required value={data.email} onChange={e => setData('email', e.target.value)} className="w-full bg-transparent border-b border-white/10 py-3 md:py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs" placeholder="your@email.com" />
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30">Phone / WhatsApp</label>
                                            <input type="tel" required value={data.phone} onChange={e => setData('phone', e.target.value)} className="w-full bg-transparent border-b border-white/10 py-3 md:py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs" placeholder="+62..." />
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30">Subject</label>
                                            <input type="text" required value={data.subject} onChange={e => setData('subject', e.target.value)} className="w-full bg-transparent border-b border-white/10 py-3 md:py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs" placeholder="What are we discussing?" />
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] md:text-[10px] uppercase tracking-widest text-white/30">Message</label>
                                            <textarea required value={data.message} onChange={e => setData('message', e.target.value)} className="w-full bg-transparent border-b border-white/10 py-3 md:py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs min-h-[100px]" placeholder="Tell us about your project..."></textarea>
                                        </div>
                                        <button type="submit" disabled={processing} className="w-full bg-white text-black py-4 md:py-6 text-[9px] md:text-[10px] font-black uppercase tracking-[0.3em] hover:bg-red-600 hover:text-white transition-all">Send Message</button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </main>

                    {/* Footer */}
                    <Footer settings={settings} getSetting={getSetting} />
                </div>
            </div>
        </div>
    );
}
