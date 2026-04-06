import { Head, Link } from '@inertiajs/react';
import React, { useEffect } from 'react';
import SocialIcons from '@/Components/Content/SocialIcons';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Show({ project, related = [], settings = {} }) {
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

    const getImg = (path) => {
        if (!path) return null;
        if (typeof path === 'string' && path.startsWith('http')) return path;
        return `/storage/${path}`;
    };

    const siteName = getSetting('site_name', '3FLO');
    const bannerUrl = project.banner_image ? getImg(project.banner_image) : getImg(project.gallery?.[0]);

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`${project.title} - ${siteName}`} />

            <Navbar settings={settings} />

            <main>
                {/* Hero Case Study */}
                <section className="relative h-[90vh] flex items-center px-6 md:px-32 overflow-hidden">
                    <div className="absolute inset-0 z-0">
                        {bannerUrl ? (
                            <img src={bannerUrl} alt={project.title} className="w-full h-full object-cover opacity-50" />
                        ) : (
                            <div className="w-full h-full bg-gradient-to-br from-red-600/20 to-black"></div>
                        )}
                        <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-transparent to-transparent"></div>
                    </div>

                    <div className="relative z-10 max-w-5xl">
                        <div className="flex items-center gap-4 mb-8">
                            <span className="text-[10px] font-bold tracking-[0.5em] text-red-600 uppercase">Case Study</span>
                            <div className="h-[1px] w-12 bg-white/20"></div>
                        </div>
                        <h1 className="text-4xl md:text-[10vw] font-black tracking-tighter uppercase mb-6 md:mb-10 leading-[0.8]">
                            {project.title}
                        </h1>
                        <div className="flex flex-wrap gap-12 mt-16 pb-12 border-b border-white/5">
                            <div>
                                <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Client</h4>
                                <p className="text-xl font-bold uppercase tracking-tight">{project.client || 'Confidential'}</p>
                            </div>
                            {project.completion_date && (
                                <div>
                                    <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Date</h4>
                                    <p className="text-xl font-bold uppercase tracking-tight">
                                        {new Date(project.completion_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
                                    </p>
                                </div>
                            )}
                            <div>
                                <h4 className="text-[10px] uppercase tracking-widest text-white/30 mb-2">Capabilities</h4>
                                <div className="flex flex-wrap gap-2">
                                    {project.services?.map(svc => (
                                        <span key={svc.id} className="text-[10px] border border-white/10 px-3 py-1 rounded-full uppercase tracking-widest text-white/60">
                                            {svc.title}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Summary & Details */}
                <section className="px-6 md:px-32 py-32 grid grid-cols-1 lg:grid-cols-12 gap-20">
                    <div className="lg:col-span-8">
                        <h2 className="text-3xl font-light text-white/90 leading-relaxed mb-20 italic">
                            {project.description}
                        </h2>
                        
                        <div className="prose prose-invert prose-red max-w-none">
                            <div 
                                className="text-white/60 text-lg leading-relaxed space-y-12 rich-text-content"
                                dangerouslySetInnerHTML={{ __html: project.content }} 
                            />
                        </div>

                        {/* Visual Archive (Gallery) */}
                        <div className="mt-32 space-y-20">
                            {project.gallery?.map((img, idx) => (
                                <div key={idx} className="relative aspect-video overflow-hidden bg-white/5 border border-white/5">
                                    <img src={getImg(img)} alt={`${project.title} - Artifact 0${idx + 1}`} className="w-full h-full object-cover" />
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Sidebar: The Humans Behind */}
                    <div className="lg:col-span-4">
                        <div className="sticky top-40 space-y-20">
                            <div>
                                <h3 className="text-xs font-bold tracking-[0.4em] uppercase text-red-600 mb-8">The Collective</h3>
                                <div className="space-y-8">
                                    {project.team_members?.map(member => (
                                        <Link 
                                            key={member.id} 
                                            href={`/team/${member.slug}`}
                                            className="group flex items-center gap-4"
                                        >
                                            <div className="size-12 rounded-full overflow-hidden bg-white/10 flex-shrink-0 grayscale group-hover:grayscale-0 transition-all">
                                                {member.photo ? (
                                                    <img src={`/storage/${member.photo}`} alt={member.name} className="w-full h-full object-cover" />
                                                ) : (
                                                    <div className="w-full h-full flex items-center justify-center text-[8px] bg-red-600/20 uppercase">3F</div>
                                                )}
                                            </div>
                                            <div>
                                                <h5 className="text-xs font-bold uppercase tracking-widest group-hover:text-red-600 transition-colors">{member.name}</h5>
                                                <p className="text-[9px] text-white/30 uppercase tracking-widest mt-1">{member.position}</p>
                                            </div>
                                        </Link>
                                    ))}
                                    {(!project.team_members || project.team_members.length === 0) && (
                                        <p className="text-[10px] text-white/20 uppercase tracking-widest italic">An anonymous artifact from the forge.</p>
                                    )}
                                </div>
                            </div>

                            <Link 
                                href="/contact"
                                className="block w-full py-8 border border-white/10 hover:border-red-600 hover:bg-red-600 group transition-all text-center relative overflow-hidden"
                            >
                                <span className="relative z-10 text-[10px] font-bold uppercase tracking-[0.5em] group-hover:text-white transition-colors">Start a project</span>
                                <div className="absolute inset-0 bg-red-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Related Works (Archive) */}
                {related.length > 0 && (
                    <section className="px-6 md:px-32 py-40 border-t border-white/5 bg-[#080808]">
                        <div className="max-w-7xl mx-auto">
                            <h2 className="text-xs uppercase tracking-[1em] mb-20 text-red-600 font-black">Related Archive</h2>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
                                {related.map(item => (
                                    <Link key={item.id} href={`/projects/${item.slug}`} className="group block">
                                        <div className="aspect-[4/3] overflow-hidden bg-white/5 mb-6">
                                            {item.gallery?.[0] && (
                                                <img src={item.gallery[0]} alt={item.title} className="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700" />
                                            )}
                                        </div>
                                        <h3 className="text-sm font-black uppercase tracking-[0.2em] group-hover:text-red-600 transition-colors">{item.title}</h3>
                                        <p className="text-[9px] text-white/20 uppercase tracking-widest mt-2">{item.client}</p>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </section>
                )}
            </main>

            {/* Footer */}
            <Footer settings={settings} getSetting={getSetting} />

            <style>{`
                .rich-text-content h2, .rich-text-content h3 {
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    color: white;
                }
                .rich-text-content h2 { font-size: 3rem; line-height: 1; }
                .rich-text-content p { color: rgba(255,255,255,0.7); line-height: 2; font-size: 1.1rem; }
                .rich-text-content ul { list-style: none; padding-left: 0; }
                .rich-text-content li { 
                    position: relative; 
                    padding-left: 2rem; 
                    margin-bottom: 1rem; 
                    color: rgba(255,255,255,0.6);
                }
                .rich-text-content li::before {
                    content: '—';
                    position: absolute;
                    left: 0;
                    color: #dc2626;
                    font-weight: 900;
                }
            `}</style>
        </div>
    );
}
