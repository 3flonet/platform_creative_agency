import { Head, Link, useForm } from '@inertiajs/react';
import React, { useEffect } from 'react';
import SocialIcons from '@/Components/Content/SocialIcons';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Contact({ settings = {} }) {
    const { data, setData, post, processing, errors, reset, wasSuccessful } = useForm({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
    });

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

    const submitContact = (e) => {
        e.preventDefault();
        post(route('contact.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`Get in Touch - ${siteName}`} />

            <Navbar settings={settings} />

            <main className="pt-32">
                <section className="min-h-screen px-6 md:px-32 py-20">
                    <div className="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 md:gap-20">
                        <div>
                            <h2 className="text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">01. Connection</h2>
                            <h1 className="text-4xl md:text-[5vw] font-black uppercase tracking-tighter italic mb-10 leading-none">Let's <br/>Build <br/>Together.</h1>
                            
                            <div className="space-y-8 md:space-y-12 mt-12 md:mt-20">
                                {getSetting('contact_email') && (
                                    <div>
                                        <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Email</h4>
                                        <a href={`mailto:${getSetting('contact_email')}`} className="text-2xl font-light hover:text-red-600 transition-colors uppercase tracking-tight">{getSetting('contact_email')}</a>
                                    </div>
                                )}
                                {getSetting('contact_phone') && (
                                    <div>
                                        <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Phone</h4>
                                        <a href={`tel:${getSetting('contact_phone')}`} className="text-2xl font-light hover:text-red-600 transition-colors uppercase tracking-tight">{getSetting('contact_phone')}</a>
                                    </div>
                                )}
                                {getSetting('contact_address') && (
                                    <div>
                                        <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Studio</h4>
                                        <p className="text-sm font-light text-white/60 leading-relaxed uppercase tracking-widest max-w-sm">{getSetting('contact_address')}</p>
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="bg-white/5 p-6 md:p-10 backdrop-blur-3xl border border-white/5">
                            {wasSuccessful ? (
                                <div className="h-full flex flex-col items-center justify-center text-center space-y-6">
                                    <div className="size-20 rounded-full border border-red-600 flex items-center justify-center text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" className="bi bi-check2" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                        </svg>
                                    </div>
                                    <h2 className="text-2xl font-black uppercase tracking-tighter">Message Sent!</h2>
                                    <p className="text-white/40 text-xs tracking-widest uppercase">We'll get back to you shortly.</p>
                                    <button onClick={() => reset()} className="text-[10px] font-bold text-red-600 uppercase tracking-widest border-b border-red-600/30 pb-1">Send another</button>
                                </div>
                            ) : (
                                <form onSubmit={submitContact} className="space-y-8">
                                    <div className="space-y-2">
                                        <label className="text-[10px] uppercase tracking-widest text-white/30">Your Name</label>
                                        <input 
                                            type="text" 
                                            required
                                            value={data.name}
                                            onChange={e => setData('name', e.target.value)}
                                            className="w-full bg-transparent border-b border-white/10 py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs"
                                            placeholder="Enter your name"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] uppercase tracking-widest text-white/30">Email Address</label>
                                        <input 
                                            type="email" 
                                            required
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            className="w-full bg-transparent border-b border-white/10 py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs"
                                            placeholder="your@email.com"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] uppercase tracking-widest text-white/30">Phone / WhatsApp</label>
                                        <input 
                                            type="tel" 
                                            required
                                            value={data.phone}
                                            onChange={e => setData('phone', e.target.value)}
                                            className="w-full bg-transparent border-b border-white/10 py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs"
                                            placeholder="+62..."
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] uppercase tracking-widest text-white/30">Subject</label>
                                        <input 
                                            type="text" 
                                            required
                                            value={data.subject}
                                            onChange={e => setData('subject', e.target.value)}
                                            className="w-full bg-transparent border-b border-white/10 py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs"
                                            placeholder="What are we discussing?"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] uppercase tracking-widest text-white/30">Message</label>
                                        <textarea 
                                            required
                                            value={data.message}
                                            onChange={e => setData('message', e.target.value)}
                                            rows="4"
                                            className="w-full bg-transparent border-b border-white/10 py-4 focus:outline-none focus:border-red-600 transition-colors uppercase tracking-widest text-xs resize-none"
                                            placeholder="Tell us about your project"
                                        ></textarea>
                                    </div>

                                    <button 
                                        type="submit" 
                                        disabled={processing}
                                        className="w-full py-6 bg-red-600 hover:bg-red-700 text-xs font-bold uppercase tracking-[0.5em] transition-all disabled:opacity-50"
                                    >
                                        {processing ? 'Sending...' : 'Send Inquiry'}
                                    </button>
                                </form>
                            )}
                        </div>
                    </div>
                </section>
            </main>

            {/* Footer */}
            <Footer settings={settings} getSetting={getSetting} />
        </div>
    );
}
