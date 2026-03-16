import React from 'react';
import { Head, Link } from '@inertiajs/react';
import SocialIcons from '@/Components/Content/SocialIcons';
import { motion, AnimatePresence, LayoutGroup } from 'framer-motion';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Index({ articles, categories, settings = {} }) {
    const getSetting = (key, fallback) => {
        if (!settings || typeof settings !== 'object') return fallback;
        const val = settings[key] || fallback;
        if (typeof val === 'string' && val.includes('/') && !val.startsWith('http')) {
            return `/storage/${val}`;
        }
        return val;
    };

    const siteName = getSetting('site_name', '3FLO.');
    const siteLogo = getSetting('site_logo', null);

    return (
        <div className="bg-[#050505] min-h-screen text-white selection:bg-red-600">
            <Head>
                <title>{`Journal | ${siteName}`}</title>
            </Head>

            <div className="relative z-10">
                <Navbar settings={settings} />

                <main className="pt-40 pb-20 px-6 md:px-32">
                    <header className="max-w-7xl mx-auto mb-20">
                        <motion.h2 
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            className="text-xs uppercase tracking-[1em] mb-6 text-red-600 font-black"
                        >
                            {getSetting('journal_label', 'The Journal')}
                        </motion.h2>
                        <motion.h1 
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.2 }}
                            className="text-4xl md:text-[8vw] font-black uppercase tracking-tighter italic leading-[0.8]"
                        >
                            Stories & <br />
                            <span className="text-outline-white">Insights.</span>
                        </motion.h1>

                        <div className="mt-12 flex flex-wrap gap-4">
                            <Link 
                                href={route('journal.index')}
                                preserveState
                                preserveScroll
                                only={['articles']}
                                className={`px-6 py-2 rounded-full border text-[10px] font-bold uppercase tracking-widest transition-all duration-500 ${!route().params.category ? 'bg-red-600 text-white border-red-600 shadow-[0_0_20px_rgba(220,38,38,0.3)]' : 'border-white/10 hover:border-white text-white/50 hover:text-white'}`}
                            >
                                All
                            </Link>
                            {categories.map(cat => (
                                <Link 
                                    key={cat.id}
                                    href={route('journal.index', { category: cat.slug })}
                                    preserveState
                                    preserveScroll
                                    only={['articles']}
                                    className={`px-6 py-2 rounded-full border text-[10px] font-bold uppercase tracking-widest transition-all duration-500 ${route().params.category === cat.slug ? 'bg-red-600 text-white border-red-600 shadow-[0_0_20px_rgba(220,38,38,0.3)]' : 'border-white/10 hover:border-white text-white/50 hover:text-white'}`}
                                >
                                    {cat.name}
                                </Link>
                            ))}
                        </div>
                    </header>

                    <LayoutGroup>
                        <motion.div 
                            layout
                            className="max-w-7xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-12"
                        >
                            <AnimatePresence mode="popLayout">
                                {articles.data.map((article, index) => (
                                    <motion.div 
                                        key={article.id}
                                        layout
                                        initial={{ opacity: 0, scale: 0.9, y: 20 }}
                                        animate={{ opacity: 1, scale: 1, y: 0 }}
                                        exit={{ opacity: 0, scale: 0.9, y: 20 }}
                                        transition={{ 
                                            duration: 0.4,
                                            delay: index * 0.05,
                                            ease: [0.23, 1, 0.32, 1]
                                        }}
                                        className="group"
                                    >
                                        <Link href={route('journal.show', article.slug)} className="block space-y-6">
                                            <div className="aspect-[4/5] overflow-hidden bg-white/5 relative border border-white/5">
                                                {article.thumbnail ? (
                                                    <img 
                                                        src={`/storage/${article.thumbnail}`} 
                                                        alt={article.title} 
                                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                    />
                                                ) : (
                                                    <div className="w-full h-full flex items-center justify-center text-white/5 font-black text-6xl italic">3FLO.</div>
                                                )}
                                                <div className="absolute top-6 left-6">
                                                    <span className="bg-red-600 text-white text-[9px] font-bold uppercase tracking-[0.3em] px-4 py-1.5">
                                                        {article.category?.name}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="space-y-4">
                                                <h3 className="text-3xl font-black uppercase tracking-tighter leading-none group-hover:text-red-600 transition-colors">
                                                    {article.title}
                                                </h3>
                                                <p className="text-white/40 text-xs uppercase tracking-widest line-clamp-3 leading-relaxed">
                                                    {article.meta_description}
                                                </p>
                                                <div className="pt-6 border-t border-white/10 flex justify-between items-center">
                                                    <span className="text-[10px] text-white/20 uppercase tracking-[0.2em]">
                                                        {new Date(article.published_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
                                                    </span>
                                                    <span className="text-[10px] font-black uppercase tracking-widest group-hover:translate-x-2 transition-transform">
                                                        Read Story →
                                                    </span>
                                                </div>
                                            </div>
                                        </Link>
                                    </motion.div>
                                ))}
                            </AnimatePresence>
                        </motion.div>
                    </LayoutGroup>

                    {articles.links.length > 3 && (
                        <div className="mt-20 flex justify-center gap-2">
                             {articles.links.map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-4 py-2 text-[10px] font-bold uppercase tracking-widest transition-all ${link.active ? 'text-red-600' : 'text-white/40 hover:text-white'} ${!link.url ? 'opacity-20 pointer-events-none' : ''}`}
                                />
                             ))}
                        </div>
                    )}
                </main>

                {/* Footer */}
                <Footer settings={settings} getSetting={getSetting} />
            </div>

            <style>{`
                .text-outline-white { color: transparent; -webkit-text-stroke: 1px rgba(255,255,255,0.4); }
            `}</style>
        </div>
    );
}
