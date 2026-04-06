import React from 'react';
import { router } from '@inertiajs/react';

const TeamSection = ({ team = [] }) => {
    const navigateTo = (slug) => {
        if (!slug) return;
        router.visit(`/team/${slug}`);
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 mt-20">
            {team.map((member, idx) => (
                <div 
                    key={member.id} 
                    className="group relative block"
                >
                    {/* Member Photo */}
                    <div className="relative aspect-[3/4] overflow-hidden bg-white/5 border border-white/5 cursor-pointer" onClick={() => navigateTo(member.slug)}>
                        {member.photo ? (
                            <img 
                                src={`/storage/${member.photo}`} 
                                alt={member.name} 
                                className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100"
                            />
                        ) : (
                            <div className="w-full h-full flex items-center justify-center text-white/5 italic text-[10px] uppercase tracking-widest">
                                No Photo
                            </div>
                        )}
                        
                        {/* Hover Overlay with Socials */}
                        <div className="absolute inset-0 bg-red-600/90 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-8 translate-y-4 group-hover:translate-y-0 transition-transform">
                            <div className="flex flex-wrap gap-4 mb-6">
                                {member.instagram && (
                                    <a 
                                        href={member.instagram.startsWith('http') ? member.instagram : `https://instagram.com/${member.instagram}`} 
                                        target="_blank" 
                                        className="hover:scale-125 transition-transform"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <i className="bi bi-instagram text-xl"></i>
                                    </a>
                                )}
                                {member.linkedin && (
                                    <a 
                                        href={member.linkedin.startsWith('http') ? member.linkedin : `https://linkedin.com/in/${member.linkedin}`} 
                                        target="_blank" 
                                        className="hover:scale-125 transition-transform"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <i className="bi bi-linkedin text-xl"></i>
                                    </a>
                                )}
                                {member.twitter && (
                                    <a 
                                        href={member.twitter.startsWith('http') ? member.twitter : `https://x.com/${member.twitter}`} 
                                        target="_blank" 
                                        className="hover:scale-125 transition-transform"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <i className="bi bi-twitter-x text-xl"></i>
                                    </a>
                                )}
                                {member.github && (
                                    <a 
                                        href={member.github.startsWith('http') ? member.github : `https://github.com/${member.github}`} 
                                        target="_blank" 
                                        className="hover:scale-125 transition-transform"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <i className="bi bi-github text-xl"></i>
                                    </a>
                                )}
                                {member.dribbble && (
                                    <a 
                                        href={member.dribbble.startsWith('http') ? member.dribbble : `https://dribbble.com/${member.dribbble}`} 
                                        target="_blank" 
                                        className="hover:scale-125 transition-transform"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <i className="bi bi-dribbble text-xl"></i>
                                    </a>
                                )}
                            </div>
                            <p className="text-xs leading-relaxed font-medium uppercase tracking-widest leading-relaxed">
                                {member.bio || "Creative mind behind the scene."}
                            </p>
                        </div>
                    </div>

                    {/* Member Info */}
                    <div className="mt-8 cursor-pointer" onClick={() => navigateTo(member.slug)}>
                        <div className="flex items-center gap-3 mb-2">
                            <span className="text-[10px] font-bold tracking-[0.3em] text-red-600 uppercase">0{idx + 1}</span>
                            <div className="h-[1px] w-8 bg-white/10 group-hover:w-12 transition-all"></div>
                        </div>
                        <h3 className="text-2xl font-black uppercase tracking-tighter mb-1 group-hover:text-red-600 transition-colors">
                            {member.name}
                        </h3>
                        <p className="text-[10px] text-white/30 uppercase tracking-[0.4em] font-light">
                            {member.position}
                        </p>
                    </div>
                </div>
            ))}
        </div>
    );
};

export default TeamSection;
