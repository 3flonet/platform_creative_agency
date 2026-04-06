import { Head, Link } from '@inertiajs/react';
import React, { useEffect } from 'react';
import SocialIcons from '@/Components/Content/SocialIcons';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Show({ service, related = [], settings = {} }) {
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

    const getImg = (path) => {
        if (!path) return null;
        if (typeof path === 'string' && path.startsWith('http')) return path;
        return `/storage/${path}`;
    };

    const siteName = getSetting('site_name', '3FLO');
    const bannerUrl = service.banner_image ? getImg(service.banner_image) : null;

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`${service.title} - ${siteName}`} />

            <Navbar settings={settings} />

            <main>
                {/* Hero Section */}
                <section className="relative h-[80vh] flex items-end px-6 md:px-32 pb-24 overflow-hidden">
                    {bannerUrl ? (
                        <div className="absolute inset-0 z-0">
                            <img src={bannerUrl} alt={service.title} className="w-full h-full object-cover opacity-40" />
                            <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-transparent to-transparent"></div>
                        </div>
                    ) : (
                        <div className="absolute inset-0 z-0 bg-gradient-to-br from-red-900/20 to-transparent"></div>
                    )}

                    <div className="relative z-10 max-w-4xl">
                        <div className="flex items-center gap-4 mb-6">
                            <span className="text-[10px] font-bold tracking-[0.4em] text-red-600 uppercase">
                                {service.category?.title || 'Service'}
                            </span>
                            <div className="h-[1px] w-12 bg-white/20"></div>
                        </div>
                        <h1 className="text-4xl md:text-8xl font-black tracking-tighter uppercase mb-8 leading-[0.9]">
                            {service.title}
                        </h1>
                        <p className="text-lg md:text-xl text-white/60 font-light max-w-2xl leading-relaxed">
                            {service.description}
                        </p>
                    </div>
                </section>

                {/* Content Section */}
                <section className="px-6 md:px-32 py-32 grid grid-cols-1 lg:grid-cols-12 gap-20">
                    <div className="lg:col-span-8">
                        <div className="prose prose-invert prose-red max-w-none">
                            <div 
                                className="text-white/80 text-lg leading-relaxed space-y-8 rich-text-content"
                                dangerouslySetInnerHTML={{ __html: service.content }} 
                            />
                        </div>
                    </div>

                    <div className="lg:col-span-4">
                        <div className="sticky top-40 space-y-12">
                            {/* Capabilities / Sidebar Info */}
                            <div className="p-8 bg-white/5 border border-white/5 backdrop-blur-xl">
                                <h3 className="text-xs font-bold tracking-[0.3em] uppercase text-white/30 mb-8 pb-4 border-b border-white/10">
                                    Related Services
                                </h3>
                                <ul className="space-y-6">
                                    {related.map(item => (
                                        <li key={item.id}>
                                            <Link 
                                                href={`/services/${item.slug}`}
                                                className="group flex items-center justify-between"
                                            >
                                                <span className="text-sm font-bold uppercase tracking-widest text-white/50 group-hover:text-white transition-colors">
                                                    {item.title}
                                                </span>
                                                <div className="size-2 rounded-full bg-white/10 group-hover:bg-red-600 transition-all group-hover:scale-150"></div>
                                            </Link>
                                        </li>
                                    ))}
                                    {related.length === 0 && (
                                        <p className="text-[10px] text-white/20 uppercase tracking-widest italic">No related services found.</p>
                                    )}
                                </ul>
                            </div>

                            <Link 
                                href="/contact"
                                className="block w-full py-6 bg-red-600 hover:bg-red-700 text-center text-xs font-bold uppercase tracking-[0.5em] transition-all hover:scale-[1.02]"
                            >
                                Get in Touch
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Selected Works Section */}
                {service.projects && service.projects.length > 0 && (
                    <section className="px-6 md:px-32 py-40 border-t border-white/5">
                        <div className="max-w-7xl mx-auto">
                            <div className="flex flex-col md:flex-row justify-between items-end gap-8 mb-20">
                                <div>
                                    <h2 className="text-xs uppercase tracking-[1em] mb-10 text-red-600 font-black">03. Impact</h2>
                                    <h1 className="text-4xl md:text-[6vw] font-black uppercase tracking-tighter italic leading-none whitespace-normal md:whitespace-nowrap">Selected Works</h1>
                                </div>
                                <div className="text-white/30 text-xs uppercase tracking-widest font-light max-w-xs text-right">
                                    A collection of artifacts built through our {service.title} capabilities.
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
                                {service.projects.map(project => (
                                    <Link 
                                        key={project.id} 
                                        href={`/projects/${project.slug}`}
                                        className="group relative block"
                                    >
                                        <div className="aspect-video overflow-hidden bg-white/5 mb-8">
                                            {project.gallery && project.gallery[0] && (
                                                <img 
                                                    src={getImg(project.gallery[0])} 
                                                    alt={project.title} 
                                                    className="w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700"
                                                />
                                            )}
                                        </div>
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <h3 className="text-2xl font-black uppercase tracking-tighter mb-1 group-hover:text-red-600 transition-colors">
                                                    {project.title}
                                                </h3>
                                                <p className="text-[10px] text-white/30 uppercase tracking-[0.4em] font-light">
                                                    {project.client}
                                                </p>
                                            </div>
                                            <div className="h-[1px] w-12 bg-white/10 mt-4 group-hover:w-20 group-hover:bg-red-600 transition-all"></div>
                                        </div>
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
                .rich-text-content h1, .rich-text-content h2, .rich-text-content h3 {
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    color: white;
                    margin-top: 2rem;
                    margin-bottom: 1rem;
                }
                .rich-text-content h2 { font-size: 2.25rem; line-height: 1; }
                .rich-text-content p { color: rgba(255,255,255,0.7); line-height: 1.8; }
                .rich-text-content ul { list-style: none; padding-left: 0; }
                .rich-text-content li { 
                    position: relative; 
                    padding-left: 1.5rem; 
                    margin-bottom: 0.75rem; 
                    color: rgba(255,255,255,0.7);
                }
                .rich-text-content li::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0.6em;
                    width: 0.5rem;
                    height: 1px;
                    background: #dc2626;
                }
            `}</style>
        </div>
    );
}
