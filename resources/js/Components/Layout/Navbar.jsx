import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';

export default function Navbar({ settings = {} }) {
    const { url, props } = usePage();
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    
    // Lock scroll when menu is open
    useEffect(() => {
        if (isMenuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }
    }, [isMenuOpen]);

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

    const siteName = getSetting('site_name', '3FLO');
    const siteLogo = getSetting('site_logo', null);

    const ensureNumber = (label, number) => {
        if (!label) return `${number}. `;
        if (/^\d+\./.test(label)) return label;
        return `${number}. ${label}`;
    };

    const navLinks = [
        { name: ensureNumber(getSetting('section_1_label', 'Discovery'), '01'), href: '/', id: 'insight' },
        { name: ensureNumber(getSetting('section_2_label', 'Capabilities'), '02'), href: '/services', id: 'services' },
        { name: ensureNumber(getSetting('section_3_label', 'Archive'), '03'), href: '/projects', id: 'projects' },
        { name: ensureNumber(getSetting('section_4_label', 'Collective'), '04'), href: '/team', id: 'team' },
        { name: ensureNumber(getSetting('journal_label', 'Journal'), '05'), href: '/journal', id: 'journal' },
        { name: ensureNumber(getSetting('section_5_label', 'Reach'), '06'), href: '/contact', id: 'contact' },
    ];

    const isPathActive = (href) => {
        if (href === '/' && (url === '/' || url === '')) return true;
        if (href === '/journal' && url.startsWith('/journal')) return true;
        if (href === '/contact' && url === '/contact') return true;
        if (href === '/services' && url.startsWith('/services')) return true;
        if (href === '/projects' && url.startsWith('/projects')) return true;
        if (href === '/team' && url.startsWith('/team')) return true;
        return false;
    };

    const handleBrandClick = () => {
        if (url === '/' || url === '') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        setIsMenuOpen(false);
    };

    return (
        <>
            <nav className={`fixed top-0 left-0 w-full z-[100] px-6 md:px-12 py-6 md:py-8 flex justify-between items-center transition-all duration-500 ${isMenuOpen ? 'bg-transparent' : 'mix-blend-difference'}`}>
                <Link 
                    href="/" 
                    onClick={handleBrandClick}
                    className="hover:text-red-600 transition-colors uppercase text-white relative z-[110]"
                >
                    {siteLogo ? (
                        <img src={siteLogo} alt={siteName} className="h-8 md:h-16 w-auto object-contain" />
                    ) : (
                        <div className="text-xl md:text-2xl font-black tracking-tighter">{siteName}</div>
                    )}
                </Link>
                
                {/* Desktop Menu */}
                <div className="hidden md:flex gap-12 text-[10px] font-bold tracking-[0.4em] uppercase items-center">
                    {navLinks.map((link) => {
                        const active = isPathActive(link.href);
                        return (
                            <div key={link.id} className="relative flex flex-col items-center">
                                <Link 
                                    href={active ? '#' : link.href} 
                                    onClick={(e) => active && e.preventDefault()}
                                    className={`transition-all duration-700 font-black ${
                                        active 
                                        ? 'opacity-20 cursor-default pointer-events-none text-white' 
                                        : 'text-white hover:text-red-600'
                                    }`}
                                >
                                    {link.name}
                                </Link>
                                {active && (
                                    <motion.div 
                                        layoutId="nav-dot"
                                        initial={{ scale: 0, opacity: 0 }}
                                        animate={{ scale: 1, opacity: 1 }}
                                        className="absolute -bottom-3 w-1.5 h-1.5 bg-red-600 rounded-full shadow-[0_0_10px_#dc2626]"
                                    />
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Mobile Menu Toggle */}
                <button 
                    onClick={() => setIsMenuOpen(!isMenuOpen)}
                    className="md:hidden relative z-[110] flex flex-col gap-1.5 p-2"
                    aria-label="Toggle Menu"
                >
                    <motion.span 
                        animate={isMenuOpen ? { rotate: 45, y: 7 } : { rotate: 0, y: 0 }}
                        className="w-6 h-0.5 bg-white block"
                    />
                    <motion.span 
                        animate={isMenuOpen ? { opacity: 0 } : { opacity: 1 }}
                        className="w-6 h-0.5 bg-white block"
                    />
                    <motion.span 
                        animate={isMenuOpen ? { rotate: -45, y: -7 } : { rotate: 0, y: 0 }}
                        className="w-6 h-0.5 bg-white block"
                    />
                </button>
            </nav>

            {/* Mobile Menu Overlay */}
            <AnimatePresence>
                {isMenuOpen && (
                    <motion.div 
                        initial={{ opacity: 0, x: '100%' }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: '100%' }}
                        transition={{ type: 'spring', damping: 25, stiffness: 200 }}
                        className="fixed inset-0 z-[105] bg-[#050505] flex flex-col justify-center px-12"
                    >
                        <div className="flex flex-col gap-8">
                            {navLinks.map((link, index) => {
                                const active = isPathActive(link.href);
                                return (
                                    <motion.div
                                        key={link.id}
                                        initial={{ opacity: 0, x: 20 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        transition={{ delay: 0.1 + index * 0.1 }}
                                    >
                                        <Link
                                            href={link.href}
                                            onClick={() => setIsMenuOpen(false)}
                                            className={`text-3xl font-black uppercase tracking-tighter italic ${active ? 'text-red-600' : 'text-white'}`}
                                        >
                                            {link.name}
                                        </Link>
                                    </motion.div>
                                );
                            })}
                        </div>

                        <motion.div 
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.8 }}
                            className="absolute bottom-12 left-12 right-12 flex justify-between items-center"
                        >
                            <div className="text-[8px] uppercase tracking-[0.5em] text-white/20">
                                {siteName} Creative Agency
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
        </>
    );
}
