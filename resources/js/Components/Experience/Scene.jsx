import React, { useLayoutEffect, useRef, Suspense } from 'react';
import { Canvas, useThree } from '@react-three/fiber';
import { PerspectiveCamera, Environment, ContactShadows } from '@react-three/drei';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import FloatingObject from './FloatingObject';

gsap.registerPlugin(ScrollTrigger);

const ScrollHandler = ({ groupRef, settings, isReady }) => {
    const { camera } = useThree();

    const parseConfig = (key, fallback) => {
        try {
            const val = settings[key];
            if (!val) return fallback;
            return JSON.parse(val);
        } catch (e) {
            return fallback;
        }
    };

    const getScale = (key, fallback) => {
        const val = settings[key];
        return val ? parseFloat(val) : fallback;
    };

    useLayoutEffect(() => {
        if (!groupRef.current || !camera || !isReady) return;

        let ctx = gsap.context(() => {
            // Kita beri sedikit delay agar transisi preloader selesai sempurna
            gsap.delayedCall(0.1, () => {
                ScrollTrigger.refresh();
                
                const tl = gsap.timeline({
                    scrollTrigger: {
                        trigger: "#main-content",
                        start: "top top",
                        end: "bottom bottom",
                        scrub: 1.5,
                        invalidateOnRefresh: true,
                    }
                });

                // Set Initial State (Section 1)
                const pos1 = parseConfig('3d_pos_1', {x: 0, y: 0, z: 0});
                const rot1 = parseConfig('3d_rot_1', {x: 0.2, y: 0.4, z: 0});
                const scale1 = getScale('3d_scale_1', 1.2);

                gsap.set(groupRef.current.position, { x: pos1.x, y: pos1.y, z: pos1.z });
                gsap.set(groupRef.current.rotation, { x: rot1.x, y: rot1.y, z: rot1.z });
                gsap.set(groupRef.current.scale, { x: scale1, y: scale1, z: scale1 });

                // Create Dynamic Transitions
                for (let i = 2; i <= 6; i++) {
                    const pos = parseConfig(`3d_pos_${i}`, {x: 0, y: 0, z: 0});
                    const rot = parseConfig(`3d_rot_${i}`, {x: 0, y: 0, z: 0});
                    const scale = getScale(`3d_scale_${i}`, 1);

                    tl.to(groupRef.current.position, { 
                        x: pos.x, y: pos.y, z: pos.z,
                        ease: "power2.inOut"
                    }, i - 1.5)
                    .to(groupRef.current.rotation, { 
                        x: rot.x, y: rot.y, z: rot.z,
                        ease: "power2.inOut"
                    }, i - 1.5)
                    .to(groupRef.current.scale, { 
                        x: scale, y: scale, z: scale,
                        ease: "power2.inOut"
                    }, i - 1.5);
                }
            });
        });

        return () => ctx.revert();
    }, [camera, groupRef, settings, isReady]);

    return null;
};

const Scene = ({ settings = {}, isReady = false }) => {
    const groupRef = useRef();

    return (
        <div className="fixed inset-0 z-0 bg-[#050505] pointer-events-none">
            <Canvas shadows gl={{ antialias: true, alpha: true }} dpr={[1, 2]}>
                <PerspectiveCamera makeDefault position={[0, 0, 6]} fov={45} />
                <ambientLight intensity={0.5} />
                <spotLight position={[10, 20, 10]} angle={0.15} penumbra={1} intensity={2} castShadow />
                <pointLight position={[-10, -10, -10]} intensity={1} color="#FF2D20" />
                <Suspense fallback={null}>
                    <group ref={groupRef}>
                        <FloatingObject 
                            type={settings['3d_object_type'] || 'torus_knot'} 
                            customModelUrl={settings['3d_model_custom'] ? `/storage/${settings['3d_model_custom']}` : null}
                            animate={settings['3d_animate_model'] === '1' || settings['3d_animate_model'] === 1 || settings['3d_animate_model'] === true}
                        />
                    </group>
                    <Environment preset="night" />
                </Suspense>
                <ContactShadows position={[0, -2.5, 0]} opacity={0.4} scale={15} blur={2.5} far={5} />
                <ScrollHandler groupRef={groupRef} settings={settings} isReady={isReady} />
            </Canvas>
        </div>
    );
};

export default Scene;
