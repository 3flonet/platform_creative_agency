import React, { useState, useEffect } from 'react';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Link } from '@inertiajs/react';

const ProjectGallery = ({ projects }) => {
    const [active, setActive] = useState(null);

    // Refresh ScrollTrigger when images are loaded
    const handleImageLoad = () => {
        ScrollTrigger.refresh();
    };

    const getImageUrl = (project) => {
        const image = Array.isArray(project.gallery) ? project.gallery[0] : project.gallery;
        
        if (!image) {
            return 'https://images.unsplash.com/photo-1550745165-9bc0b252726f';
        }
        
        if (typeof image === 'string' && (image.startsWith('http') || image.startsWith('https'))) {
            return image;
        }
        
        return `/storage/${image}`;
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {projects && projects.length > 0 ? (
                projects.map((project) => {
                    const imgSource = getImageUrl(project);
                    
                    return (
                        <Link 
                            key={project.id}
                            href={`/projects/${project.slug}`}
                            className="group relative aspect-[4/5] overflow-hidden bg-zinc-900 cursor-pointer block"
                            onMouseEnter={() => setActive(project.id)}
                            onMouseLeave={() => setActive(null)}
                        >
                            <img 
                                src={imgSource} 
                                onLoad={handleImageLoad}
                                className="absolute inset-0 size-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-700 ease-expo"
                                alt={project.title}
                                onError={(e) => { 
                                    if (e.target.src !== 'https://images.unsplash.com/photo-1550745165-9bc0b252726f') {
                                        e.target.src = 'https://images.unsplash.com/photo-1550745165-9bc0b252726f';
                                    }
                                }}
                            />

                            <div className="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-80 group-hover:opacity-40 transition-opacity"></div>

                            <div className="absolute inset-x-0 bottom-0 p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <div className="flex flex-wrap gap-2 mb-3">
                                    {project.services?.map(s => (
                                        <span key={s.id} className="text-[7px] font-black tracking-widest text-white/40 border border-white/10 px-2 py-0.5 uppercase">
                                            {s.title}
                                        </span>
                                    ))}
                                </div>
                                <p className="text-[10px] font-bold tracking-[0.3em] text-red-600 mb-2 uppercase">{project.client}</p>
                                <h4 className="text-2xl font-black uppercase tracking-tighter mb-4">{project.title}</h4>
                                <div className="h-[2px] w-0 group-hover:w-full bg-red-600 transition-all duration-500"></div>
                                <p className="mt-4 text-xs font-light text-white/50 opacity-0 group-hover:opacity-100 transition-opacity line-clamp-2">
                                    {project.description}
                                </p>
                            </div>

                            <div className="absolute top-6 right-6 flex flex-col gap-2 items-end">
                                <div className="bg-red-600 px-3 py-1 text-[8px] font-black uppercase tracking-widest">
                                    {project.id === active ? 'Exploring' : 'Project'}
                                </div>
                            </div>
                        </Link>
                    );
                })
            ) : (
                <div className="col-span-full py-20 text-center opacity-20 italic">
                    No projects found in the archive.
                </div>
            )}
        </div>
    );
};

export default ProjectGallery;
