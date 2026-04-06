import { Head, Link } from '@inertiajs/react';
import React, { useEffect } from 'react';
import SocialIcons from '@/Components/Content/SocialIcons';
import Lenis from 'lenis';
import Navbar from '@/Components/Layout/Navbar';
import Footer from '@/Components/Layout/Footer';

export default function Show({ member, settings = {} }) {
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
    const photoUrl = member.photo ? getImg(member.photo) : null;

    return (
        <div className="bg-[#050505] text-white min-h-screen selection:bg-red-600 selection:text-white">
            <Head title={`${member.name} - ${siteName}`} />

            <Navbar settings={settings} />

            <main className="pt-32">
                <section className="px-6 md:px-32 py-20">
                    <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-20 items-start">
                        
                        {/* Member Photo Side */}
                        <div className="lg:col-span-5">
                            <div className="relative aspect-[3/4] overflow-hidden bg-white/5 border border-white/5 group">
                                {photoUrl ? (
                                    <img 
                                        src={photoUrl} 
                                        alt={member.name} 
                                        className="w-full h-full object-cover grayscale transition-all duration-700 group-hover:grayscale-0 group-hover:scale-105" 
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-white/5 uppercase tracking-[1em] italic text-xs">No Photo</div>
                                )}
                            </div>
                            
                            <div className="mt-12 flex flex-wrap gap-8 items-center pt-8 border-t border-white/5">
                                <span className="text-[10px] font-bold tracking-[0.3em] uppercase text-white/20">Connect:</span>
                                {member.instagram && (
                                    <a href={member.instagram} target="_blank" className="text-white/40 hover:text-red-600 transition-all hover:scale-125">
                                        <i className="bi bi-instagram text-2xl"></i>
                                    </a>
                                )}
                                {member.linkedin && (
                                    <a href={member.linkedin} target="_blank" className="text-white/40 hover:text-red-600 transition-all hover:scale-125">
                                        <i className="bi bi-linkedin text-2xl"></i>
                                    </a>
                                )}
                                {member.twitter && (
                                    <a href={member.twitter} target="_blank" className="text-white/40 hover:text-red-600 transition-all hover:scale-125">
                                        <i className="bi bi-twitter-x text-2xl"></i>
                                    </a>
                                )}
                                {member.github && (
                                    <a href={member.github} target="_blank" className="text-white/40 hover:text-red-600 transition-all hover:scale-125">
                                        <i className="bi bi-github text-2xl"></i>
                                    </a>
                                )}
                                {member.dribbble && (
                                    <a href={member.dribbble} target="_blank" className="text-white/40 hover:text-red-600 transition-all hover:scale-125">
                                        <i className="bi bi-dribbble text-2xl"></i>
                                    </a>
                                )}
                            </div>
                        </div>

                        {/* Member Info Side */}
                        <div className="lg:col-span-7 pt-10 lg:pt-0">
                            <h2 className="text-xs font-bold tracking-[0.5em] text-red-600 uppercase mb-6">Team Member</h2>
                            <h1 className="text-4xl md:text-9xl font-black tracking-tighter uppercase mb-6 leading-[0.8]">
                                {member.name}
                            </h1>
                            <h3 className="text-2xl font-light text-white/40 uppercase tracking-widest mb-12">
                                {member.position}
                            </h3>
                            
                            <div className="prose prose-invert max-w-none">
                                <p className="text-xl font-light text-white/70 leading-relaxed max-w-2xl italic">
                                    "{member.bio || 'Passionate about creating exceptional digital experiences and pushing the boundaries of creativity.'}"
                                </p>
                            </div>

                            <div className="mt-32">
                                <h4 className="text-[10px] font-bold tracking-[0.4em] uppercase text-white/20 mb-12 pb-4 border-b border-white/5">
                                    Contributing Projects
                                </h4>
                                
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {member.projects && member.projects.length > 0 ? (
                                        member.projects.map(project => (
                                            <Link 
                                                key={project.id} 
                                                href={`/projects/${project.slug}`}
                                                className="group/item block"
                                            >
                                                <div className="aspect-video overflow-hidden bg-white/5 mb-6">
                                                    {project.gallery && project.gallery[0] && (
                                                        <img 
                                                            src={getImg(project.gallery[0])} 
                                                            alt={project.title} 
                                                            className="w-full h-full object-cover opacity-60 group-hover/item:opacity-100 transition-opacity duration-700 group-hover/item:scale-105 transition-transform" 
                                                        />
                                                    )}
                                                </div>
                                                <h5 className="text-sm font-black uppercase tracking-widest group-hover/item:text-red-600 transition-colors">
                                                    {project.title}
                                                </h5>
                                                <p className="text-[10px] text-white/20 uppercase tracking-widest mt-1">
                                                    {project.client}
                                                </p>
                                            </Link>
                                        ))
                                    ) : (
                                        <div className="col-span-2 text-white/10 italic text-sm tracking-widest uppercase">
                                            No public projects archived yet.
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            {/* Footer */}
            <Footer settings={settings} getSetting={getSetting} />
        </div>
    );
}
