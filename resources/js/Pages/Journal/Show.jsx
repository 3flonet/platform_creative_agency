import React from 'react';
import { Head, Link } from '@inertiajs/react';
import SocialIcons from '@/Components/Content/SocialIcons';
import { motion } from 'framer-motion';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Show({ article, relatedArticles, settings = {} }) {
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
        <div className="bg-[#050505] min-h-screen text-white selection:bg-red-600 font-sans">
            <Head>
                <title>{`${article.title} | Journal - ${siteName}`}</title>
                <meta name="description" content={article.meta_description} />
                <meta name="keywords" content={article.meta_keywords} />
            </Head>

            <div className="relative z-10">
                <Navbar settings={settings} />

                <main className="pt-40 pb-40">
                    <article className="max-w-4xl mx-auto px-6">
                        <header className="mb-20">
                            <motion.div
                                initial={{ opacity: 0, scaleX: 0 }}
                                animate={{ opacity: 1, scaleX: 1 }}
                                className="h-[1px] w-24 bg-red-600 mb-10 origin-left"
                            ></motion.div>
                            
                            <motion.div 
                                initial={{ opacity: 0, y: 30 }}
                                animate={{ opacity: 1, y: 0 }}
                                className="flex items-center gap-4 mb-8"
                            >
                                <span className="text-[10px] font-bold uppercase tracking-[0.5em] text-red-600">
                                    {article.category?.name}
                                </span>
                                <span className="h-[1px] w-8 bg-white/20"></span>
                                <span className="text-[10px] font-bold uppercase tracking-[0.5em] text-white/40">
                                    {new Date(article.published_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
                                </span>
                            </motion.div>

                            <motion.h1 
                                initial={{ opacity: 0, y: 30 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: 0.1 }}
                                className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic leading-[0.9] mb-12"
                            >
                                {article.title}
                            </motion.h1>

                            {article.thumbnail && (
                                <motion.div 
                                    initial={{ opacity: 0, y: 40 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ delay: 0.2 }}
                                    className="aspect-video overflow-hidden border border-white/5 mb-12"
                                >
                                    <img 
                                        src={`/storage/${article.thumbnail}`} 
                                        alt={article.title} 
                                        className="w-full h-full object-cover"
                                    />
                                </motion.div>
                            )}
                        </header>

                        <motion.div 
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.4 }}
                            className="story-content prose prose-invert prose-red max-w-none 
                            prose-h2:text-4xl prose-h2:font-black prose-h2:tracking-tighter
                            prose-p:text-lg prose-p:text-white/70 prose-p:leading-relaxed prose-p:font-light
                            prose-strong:text-white prose-strong:font-bold
                            prose-a:text-red-600 prose-a:no-underline hover:prose-a:underline
                            prose-img:border prose-img:border-white/10
                            "
                            dangerouslySetInnerHTML={{ __html: article.content }}
                        />
                    </article>

                    <section className="mt-40 max-w-7xl mx-auto px-6 border-t border-white/5 pt-20">
                        <div className="flex justify-between items-end mb-12">
                            <div>
                                <h2 className="text-xs uppercase tracking-[1em] mb-4 text-red-600 font-black">Next Stories</h2>
                                <h3 className="text-4xl font-black uppercase tracking-tighter italic">Related Insights</h3>
                            </div>
                            <Link href={route('journal.index')} className="text-[10px] font-bold uppercase tracking-widest hover:text-red-600 transition-colors">
                                View Full Archive →
                            </Link>
                        </div>

                        <div className="grid md:grid-cols-3 gap-12">
                            {relatedArticles.map((rel, i) => (
                                <motion.div 
                                    key={rel.id}
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    transition={{ delay: i * 0.1 }}
                                    viewport={{ once: true }}
                                    className="group"
                                >
                                    <Link href={route('journal.show', rel.slug)} className="block space-y-4">
                                        <div className="aspect-[16/10] overflow-hidden bg-white/5 border border-white/5">
                                            {rel.thumbnail && (
                                                <img 
                                                    src={`/storage/${rel.thumbnail}`} 
                                                    alt={rel.title} 
                                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                />
                                            )}
                                        </div>
                                        <h4 className="text-xl font-black uppercase tracking-tighter group-hover:text-red-600 transition-colors">
                                            {rel.title}
                                        </h4>
                                    </Link>
                                </motion.div>
                            ))}
                        </div>
                    </section>
                </main>

                {/* Footer */}
                <Footer settings={settings} getSetting={getSetting} />
            </div>
            <style>{`
                .text-outline-white { color: transparent; -webkit-text-stroke: 1px rgba(255,255,255,0.4); }
                .story-content h2 { 
                    font-size: 2.25rem; 
                    font-weight: 900; 
                    letter-spacing: -0.05em; 
                    margin-top: 3rem;
                    margin-bottom: 1.5rem;
                    line-height: 1;
                    color: white;
                }
                .story-content p {
                    font-size: 1.125rem;
                    line-height: 1.75;
                    color: rgba(255, 255, 255, 0.7);
                    margin-bottom: 1.5rem;
                    font-weight: 300;
                }
                .story-content strong {
                    color: white;
                    font-weight: 700;
                }
                .story-content ul {
                    list-style-type: disc;
                    padding-left: 1.5rem;
                    margin-bottom: 1.5rem;
                    color: rgba(255, 255, 255, 0.7);
                }
            `}</style>
        </div>
    );
}
