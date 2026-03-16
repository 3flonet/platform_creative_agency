import React from 'react';
import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';

const JournalSection = ({ articles = [], getSetting }) => {
    return (
        <div className="grid md:grid-cols-3 gap-8">
            {articles.map((article, index) => (
                <motion.div 
                    key={article.id}
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ delay: index * 0.2 }}
                    viewport={{ once: true }}
                    className="group"
                >
                    <Link href={route('journal.show', article.slug)} className="block space-y-6">
                        <div className="aspect-[4/5] overflow-hidden bg-white/5 relative">
                            {article.thumbnail ? (
                                <img 
                                    src={`/storage/${article.thumbnail}`} 
                                    alt={article.title} 
                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center text-white/10 font-black text-4xl italic">
                                    3FLO.
                                </div>
                            )}
                            <div className="absolute top-4 left-4">
                                <span className="bg-red-600 text-white text-[8px] font-bold uppercase tracking-[0.3em] px-3 py-1">
                                    {article.category?.name || 'Journal'}
                                </span>
                            </div>
                        </div>
                        <div className="space-y-4">
                            <h3 className="text-2xl font-black uppercase tracking-tighter leading-none group-hover:text-red-600 transition-colors">
                                {article.title}
                            </h3>
                            <p className="text-white/40 text-xs uppercase tracking-widest line-clamp-2 leading-relaxed">
                                {article.meta_description || 'Explore the creative narrative of 3flo.'}
                            </p>
                            <div className="pt-4 border-t border-white/10 flex justify-between items-center">
                                <span className="text-[9px] text-white/20 uppercase tracking-[0.2em]">
                                    {new Date(article.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </span>
                                <span className="text-[9px] font-bold uppercase tracking-widest group-hover:translate-x-2 transition-transform">
                                    Read Story →
                                </span>
                            </div>
                        </div>
                    </Link>
                </motion.div>
            ))}
        </div>
    );
};

export default JournalSection;
