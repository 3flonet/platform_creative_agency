import React from 'react';
import SocialIcons from '@/Components/Content/SocialIcons';

const Footer = ({ settings = {}, getSetting = (key, fallback) => fallback }) => {
    const siteName = getSetting('site_name', '3FLO');
    const currentYear = new Date().getFullYear();

    return (
        <footer className="py-20 px-6 md:px-32 border-t border-white/5 bg-black flex flex-col gap-12">
            <div className="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
                <div className="flex flex-col items-center md:items-start">
                    {getSetting('site_logo') ? (
                        <img src={getSetting('site_logo')} alt={siteName} className="h-12 md:h-20 w-auto object-contain mb-4" />
                    ) : (
                        <div className="text-4xl font-black tracking-tighter mb-4 uppercase">{siteName}</div>
                    )}
                    <div className="text-white/20 text-[9px] tracking-[0.5em] uppercase">
                        {getSetting('footer_text', `${getSetting('geo_placename', 'Jakarta')} • Creative Agency • ${currentYear}`)}
                    </div>
                </div>
                <SocialIcons settings={settings} getSetting={getSetting} />
            </div>
            
            <div className="pt-6 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-1">
                <div className="text-[10px] text-white/20 tracking-[0.2em] uppercase font-medium">
                    © {currentYear} {siteName} All Rights Reserved.
                </div>
                <div className="text-[10px] text-white/20 tracking-[0.2em] uppercase font-medium flex gap-4">
                    <span>Designed with Passion & Precision</span>
                    <a href="/docs" className="hover:text-red-600 transition-colors">Documentation</a>
                </div>
            </div>
        </footer>
    );
};

export default Footer;
