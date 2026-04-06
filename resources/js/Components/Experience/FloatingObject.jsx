import React, { useRef, Suspense, useEffect } from 'react';
import { useFrame } from '@react-three/fiber';
import { Float, MeshDistortMaterial, useGLTF, Center, useAnimations } from '@react-three/drei';
import * as THREE from 'three';

const CustomModel = ({ url, animate = false }) => {
    const { scene, animations } = useGLTF(url);
    const { actions, names } = useAnimations(animations, scene);
    const modelRef = useRef();

    // Pastikan model menerima bayangan dan material merespon cahaya dengan baik
    useEffect(() => {
        if (scene) {
            scene.traverse((obj) => {
                if (obj.isMesh) {
                    obj.castShadow = true;
                    obj.receiveShadow = true;
                    
                    // Fix 'holes' by rendering both sides of every material
                    if (obj.material) {
                        const materials = Array.isArray(obj.material) ? obj.material : [obj.material];
                        materials.forEach(m => {
                            m.side = THREE.DoubleSide;
                            // Ensure materials don't disappear due to lighting/clipping
                            m.shadowSide = THREE.BackSide;
                            if (m.alphaTest !== undefined) m.alphaTest = 0.5;
                        });
                    }
                }
            });
        }
        
        // Play or stop animation based on the 'animate' prop
        if (names.length > 0) {
            if (animate) {
                actions[names[0]].reset().fadeIn(0.5).play();
            } else {
                actions[names[0]].fadeOut(0.5).stop();
            }
        }
        
        return () => {
            if (names.length > 0) actions[names[0]]?.fadeOut(0.5);
        };
    }, [scene, actions, names, animate]);

    useFrame((state) => {
        if (!modelRef.current) return;
        const time = state.clock.getElapsedTime();
        // Subtle idle movement combined with model's own animation
        modelRef.current.rotation.y += Math.sin(time / 4) / 500;
    });

    return (
        <Center top>
            <primitive ref={modelRef} object={scene} />
        </Center>
    );
};

const FloatingObject = ({ type = 'torus_knot', customModelUrl = null, animate = false }) => {
    const meshRef = useRef();

    useFrame((state) => {
        if (!meshRef.current) return;
        const time = state.clock.getElapsedTime();
        meshRef.current.rotation.x = Math.cos(time / 4) / 4;
        meshRef.current.rotation.y = Math.sin(time / 2) / 4;
        meshRef.current.rotation.z = Math.sin(time / 3) / 4;
    });

    const renderGeometry = () => {
        switch (type) {
            case 'sphere':
                return <sphereGeometry args={[1, 64, 64]} />;
            case 'box':
                return <boxGeometry args={[1.5, 1.5, 1.5]} />;
            case 'octahedron':
                return <octahedronGeometry args={[1.5]} />;
            case 'torus':
                return <torusGeometry args={[1, 0.4, 32, 100]} />;
            case 'torus_knot':
            default:
                return <torusKnotGeometry args={[1, 0.3, 128, 32]} />;
        }
    };

    // Jika ada model custom, tampilkan itu di dalam Float
    if (customModelUrl && customModelUrl !== '/storage/' && customModelUrl !== '/storage/null') {
        return (
            <>
                <spotLight 
                    position={[10, 20, 10]} 
                    angle={0.15} 
                    penumbra={1} 
                    intensity={5} 
                    castShadow 
                    shadow-bias={-0.0001} 
                />
                <Float speed={1.5} rotationIntensity={0.2} floatIntensity={0.5}>
                    <Suspense fallback={<mesh>{renderGeometry()}<meshStandardMaterial wireframe color="#FF2D20" /></mesh>}>
                        <CustomModel url={customModelUrl} animate={animate} />
                    </Suspense>
                </Float>
            </>
        );
    }

    return (
        <Float speed={2} rotationIntensity={0.5} floatIntensity={0.5}>
            <mesh ref={meshRef}>
                {renderGeometry()}
                <MeshDistortMaterial 
                    color="#FF2D20" 
                    speed={2} 
                    distort={0.4} 
                    radius={1} 
                    emissive="#330000"
                    roughness={0.2}
                    metalness={0.8}
                />
            </mesh>
        </Float>
    );
};

export default FloatingObject;
