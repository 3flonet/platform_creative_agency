import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';

const IntroPreloader = ({ 
    onComplete, 
    sequence = [], 
    brand = "3FLO.COM",
    speed = 0.5,
    useLogo = false,
    logoUrl = null
}) => {
    const containerRef = useRef(null);
    const textRef = useRef(null);
    const brandRef = useRef(null);
    const logoRef = useRef(null);

    const words = sequence.length > 0 ? sequence : ["Creative", "Innovative", "Non-stop"];
    const duration = parseFloat(speed) || 0.5;

    useEffect(() => {
        // Matikan animasi jika sedang berjalan untuk menghindari penumpukan (cleanup)
        const ctx = gsap.context(() => {
            const tl = gsap.timeline({
                onComplete: () => {
                    if (onComplete) onComplete();
                }
            });

            // 1. Fase Kata-kata
            words.forEach((word) => {
                tl.to(textRef.current, {
                    duration: duration,
                    opacity: 1,
                    onStart: () => {
                       if (textRef.current) textRef.current.innerText = word;
                    },
                    ease: "power2.out"
                })
                .to(textRef.current, {
                    duration: duration,
                    opacity: 0,
                    delay: duration,
                    ease: "power2.in"
                });
            });

            // 2. Fase Reveal Brand/Logo
            if (useLogo && logoUrl) {
                // Gunakan timeline untuk transisi blur yang lebih stabil
                tl.fromTo(logoRef.current, 
                    { opacity: 0, scale: 0.8, filter: "blur(20px)" },
                    { 
                        duration: 1.5, 
                        opacity: 1, 
                        scale: 1, 
                        filter: "blur(0px)",
                        ease: "expo.out" 
                    }
                );
            } else {
                tl.to(brandRef.current, {
                    duration: 1.2,
                    opacity: 1,
                    scale: 1,
                    letterSpacing: "0.2em",
                    ease: "expo.out",
                });
            }

            // 3. Fase Exit (Layar naik)
            tl.to(containerRef.current, {
                duration: 1.2,
                y: "-100%",
                ease: "expo.inOut",
                delay: 0.8
            });
        }, containerRef);

        return () => ctx.revert();
    }, [words, duration, useLogo, logoUrl]);

    return (
        <div 
            ref={containerRef}
            className="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-black text-white overflow-hidden"
        >
            <div className="relative h-40 w-full flex items-center justify-center">
                {/* Kata-kata Sequence */}
                <div 
                    ref={textRef} 
                    className="absolute text-3xl md:text-5xl font-extralight italic opacity-0 uppercase tracking-[0.6em] text-center px-6"
                ></div>
                
                {/* Opsi Teks Brand */}
                {!useLogo && (
                    <div 
                        ref={brandRef} 
                        className="absolute text-5xl md:text-8xl font-black tracking-tighter opacity-0 scale-125 uppercase text-center"
                    >
                        {brand}
                    </div>
                )}

                {/* Opsi Logo Gambar dengan Efek Blur Gampang (Manual Blur Fallback) */}
                {useLogo && logoUrl && (
                    <div className="flex items-center justify-center w-full px-10">
                        <img 
                            ref={logoRef}
                            src={logoUrl} 
                            alt={brand}
                            className="max-h-[120px] md:max-h-[160px] w-auto object-contain opacity-0"
                            style={{ willChange: 'filter, transform, opacity' }}
                            onError={(e) => {
                                console.error("Logo failed to load:", logoUrl);
                                e.target.style.display = 'none';
                            }}
                        />
                    </div>
                )}
            </div>
            
            {/* Progress Bar Sederhana */}
            <div className="mt-16 overflow-hidden">
                <div className="h-[1px] w-32 bg-red-600 origin-left scale-x-0 animate-progress"></div>
            </div>

            <style>{`
                @keyframes progress {
                    0% { transform: scaleX(0); }
                    100% { transform: scaleX(1); }
                }
                .animate-progress {
                    /* Hitung estimasi waktu agar sinkron dengan durasi GSAP */
                    animation: progress ${(words.length * duration * 2.2) + 3}s linear forwards;
                }
            `}</style>
        </div>
    );
};

export default IntroPreloader;
