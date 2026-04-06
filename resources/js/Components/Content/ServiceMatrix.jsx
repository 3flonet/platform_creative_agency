import React from 'react';
import { Link } from '@inertiajs/react';

const ServiceMatrix = ({ services }) => {
    // Group services by category
    const grouped = services.reduce((acc, current) => {
        const catName = current.category?.title || 'Uncategorized';
        (acc[catName] = acc[catName] || { 
            title: catName, 
            description: current.category?.description,
            prefix: current.category?.prefix || 'Group', // Fallback to 'Group'
            items: [] 
        }).items.push(current);
        return acc;
    }, {});

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-12 mt-20">
            {Object.values(grouped).map((group, idx) => (
                <div key={group.title} className="group">
                    <div className="flex items-center gap-4 mb-8">
                        <span className="text-[10px] font-bold tracking-[0.4em] text-red-600 uppercase">
                            {group.prefix} {String(idx + 1).padStart(2, '0')}
                        </span>
                        <div className="h-[1px] flex-1 bg-white/10 group-hover:bg-red-600/50 transition-colors"></div>
                    </div>
                    <h3 className="text-2xl font-black uppercase tracking-tighter mb-4">{group.title}</h3>
                    {group.description && (
                        <p className="text-[10px] text-white/30 uppercase tracking-[0.2em] mb-8 font-light leading-relaxed">
                            {group.description}
                        </p>
                    )}
                    <ul className="space-y-6">
                        {group.items.map((service) => (
                            <li key={service.id}>
                                <Link 
                                    href={service.slug ? `/services/${service.slug}` : '#'} 
                                    className="flex items-start gap-4 group/item cursor-pointer"
                                >
                                    {/* Dynamic Icon or Bullet */}
                                    <div className="mt-1 flex-shrink-0">
                                        {service.icon ? (
                                            <i className={`${service.icon} text-xl group-hover/item:scale-125 transition-transform inline-block text-red-600`}></i>
                                        ) : (
                                            <div className="size-1.5 rounded-full bg-white/20 group-hover/item:bg-red-600 transition-all group-hover/item:scale-150"></div>
                                        )}
                                    </div>
                                    
                                    <div className="flex flex-col">
                                        <span className="text-sm font-black text-white/40 group-hover/item:text-white transition-colors tracking-widest uppercase">
                                            {service.title}
                                        </span>
                                        {service.description && (
                                            <p className="text-[10px] font-light text-white/20 mt-1 leading-relaxed opacity-0 group-hover/item:opacity-100 transition-opacity">
                                                {service.description}
                                            </p>
                                        )}
                                    </div>
                                </Link>
                            </li>
                        ))}
                    </ul>
                </div>
            ))}
        </div>
    );
};

export default ServiceMatrix;
