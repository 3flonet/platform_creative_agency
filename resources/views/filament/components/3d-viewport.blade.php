<style>
.viewport-container {
    height: 500px;
    display: flex;
    position: relative;
    background-color: #111827;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #374151;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
    margin-bottom: 1.5rem;
}
.viewport-toolbar {
    position: absolute;
    top: 1rem;
    z-index: 10;
    display: flex;
    gap: 0.375rem;
    background-color: rgba(0,0,0,0.6);
    padding: 0.375rem;
    border-radius: 0.5rem;
    border: 1px solid rgba(75,85,99,0.5);
    backdrop-filter: blur(8px);
}
.viewport-toolbar.top-left { left: 1rem; }
.viewport-toolbar.top-right { right: 1rem; }
.viewport-toolbar.bottom-left {
    bottom: 1rem;
    top: auto;
    left: 1rem;
    flex-direction: column;
    width: 14rem;
    color: #d1d5db;
    font-family: monospace;
    font-size: 11px;
}
.viewport-btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: bold;
    border-radius: 0.375rem;
    transition: all 0.2s;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #d1d5db;
    background: transparent;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}
.viewport-btn:hover { background-color: #1f2937; }
.viewport-btn.active-sec {
    background-color: #dc2626;
    color: white;
    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
}
.viewport-btn.active-translate { background-color: #4f46e5; color: white; }
.viewport-btn.active-rotate { background-color: #16a34a; color: white; }
.viewport-btn.active-scale { background-color: #2563eb; color: white; }
.viewport-btn.active-pan { background-color: #f59e0b; color: white; }
.viewport-btn svg {
    width: 1rem;
    height: 1rem;
    margin-right: 0.25rem;
}
.viewport-info-row { display: flex; justify-content: space-between; }
.viewport-info-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid #374151; padding-bottom: 0.25rem; margin-bottom: 0.25rem;
}
</style>

<div x-data="filament3DViewport({ wire: $wire })" x-init="initScene()" class="viewport-container" style="color: white">
    
    <!-- Toolbar Sisi Atas: Pilih Section -->
    <div class="viewport-toolbar top-left">
        <template x-for="s in [1,2,3,4,5,6]" :key="s">
            <button @click.prevent="setSection(s)" 
                    :class="activeSection === s ? 'viewport-btn active-sec' : 'viewport-btn'"
                    x-text="'Sec 0' + s"></button>
        </template>
    </div>

    <!-- Toolbar Sisi Atas: Pilih Mode Transformasi -->
    <div class="viewport-toolbar top-right">
        <button @click.prevent="setMode('pan')" :class="mode === 'pan' ? 'viewport-btn active-pan' : 'viewport-btn'" title="Pan Camera">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .15 2.25m-3.3-2.25v6.75a1.575 1.575 0 0 0 3.15 0v-6.75M16.35 11.25H16.5a1.575 1.575 0 0 1 1.575 1.575v4.288c0 1.258-.517 2.464-1.433 3.336l-3.3 3.111a.75.75 0 0 1-1.033-.005l-2.062-2.062a.75.75 0 0 1-.213-.642L9.36 17.55m6.99-6.3v-1.5a1.575 1.575 0 1 0-3.15 0v1.5m-3.15-3v-1.5a1.575 1.575 0 1 0-3.15 0v1.5m6.3 0v1.5" /></svg>
            Cam
        </button>
        <button @click.prevent="setMode('translate')" :class="mode === 'translate' ? 'viewport-btn active-translate' : 'viewport-btn'" title="Move">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
            Pos
        </button>
        <button @click.prevent="setMode('rotate')" :class="mode === 'rotate' ? 'viewport-btn active-rotate' : 'viewport-btn'" title="Rotate">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
            Rot
        </button>
        <button @click.prevent="setMode('scale')" :class="mode === 'scale' ? 'viewport-btn active-scale' : 'viewport-btn'" title="Scale">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
            Scl
        </button>
    </div>
    
    <!-- Panel Info Sinkronisasi -->
    <div class="viewport-toolbar bottom-left">
        <div class="viewport-info-header">
            <span style="font-weight:bold; color:white; text-transform:uppercase; letter-spacing:0.1em; font-size:10px;">Live Data</span>
            <span x-text="mode.toUpperCase()" style="background-color:#1f2937; padding:2px 4px; border-radius:4px; font-size:9px;"></span>
        </div>
        <div class="viewport-info-row">
            <span style="color:#f87171; font-weight:bold;">X</span>
            <span x-text="currPos.x">0</span>
        </div>
        <div class="viewport-info-row">
            <span style="color:#4ade80; font-weight:bold;">Y</span> 
            <span x-text="currPos.y">0</span>
        </div>
        <div class="viewport-info-row">
            <span style="color:#60a5fa; font-weight:bold;">Z</span> 
            <span x-text="currPos.z">0</span>
        </div>
        <div style="margin-top:8px; color:#9ca3af; font-style:italic; font-family:sans-serif; font-size:10px; line-height:1.2;">
           * Seret gizmo (panah ruang) untuk mengatur secara visual. Form akan otomatis tersinkronisasi.
        </div>
    </div>

    <!-- Canvas Container -->
    <div wire:ignore x-ref="canvasContainer" style="flex: 1; width: 100%; height: 100%; cursor: crosshair;"></div>
</div>

<script type="module">
import * as THREE from 'https://cdn.skypack.dev/three@0.136.0';
import { OrbitControls } from 'https://cdn.skypack.dev/three@0.136.0/examples/jsm/controls/OrbitControls.js';
import { TransformControls } from 'https://cdn.skypack.dev/three@0.136.0/examples/jsm/controls/TransformControls.js';

document.addEventListener('alpine:init', () => {
    // KELUARKAN objek Three.js dari State Reactive Alpine x-data (proxy)
    // Three.js tidak suka dibungkus Proxy oleh Vue/Alpine karena performa dan referensi matriks read-only-nya
    
    Alpine.data('filament3DViewport', ({ wire }) => {
        let scene, camera, renderer, orbit, control, currentMesh;
        
        return {
            activeSection: 1,
            mode: 'translate',
            currPos: { x: '0', y: '0', z: '0' },
            wire: wire,
            ignoreWatch: false,
            lastDataHash: null,
            lastModelHash: null,
            
            initScene() {
                setTimeout(async () => {
                    const container = this.$refs.canvasContainer;
                    if (!container) return;
                    
                    // 1. Renderer
                    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                    renderer.setPixelRatio(window.devicePixelRatio);
                    renderer.setSize(container.clientWidth, container.clientHeight);
                    container.appendChild(renderer.domElement);

                    // 2. Scene setup
                    scene = new THREE.Scene();
                    scene.background = new THREE.Color(0x111827); // Tailwind gray-900

                    // Grid Helper
                    const gridHelper = new THREE.GridHelper(10, 10, 0x444444, 0x222222);
                    gridHelper.position.y = -1;
                    scene.add(gridHelper);

                    // 3. Camera
                    camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 100);
                    camera.position.set(0, 0, 7);

                    // 4. Lighting
                    const ambientLight = new THREE.AmbientLight(0xffffff, 2.0); // Terangkan global light (solusi glb gelap)
                    scene.add(ambientLight);
                    
                    const dirLight = new THREE.DirectionalLight(0xffffff, 3.0);
                    dirLight.position.set(5, 10, 7);
                    scene.add(dirLight);

                    const spotLight = new THREE.SpotLight(0xffffff, 2);
                    spotLight.position.set(-10, 20, -10);
                    scene.add(spotLight);

                    // 5. Default Mesh Initialization
                    this.replaceMeshWithPrimitive('torus_knot');

                    // 6. Navigation Controls
                    orbit = new OrbitControls(camera, renderer.domElement);
                    orbit.update();
                    orbit.addEventListener('change', () => this.renderScene());

                    // 7. Transform Controls (The Gizmo)
                    control = new TransformControls(camera, renderer.domElement);
                    control.addEventListener('change', () => this.renderScene());
                    control.addEventListener('dragging-changed', (event) => {
                        orbit.enabled = !event.value; // Disable orbit saat transform
                        if (!event.value) { 
                            this.onDragEnd(); 
                        } else { 
                            this.ignoreWatch = true; 
                        }
                    });
                    
                    control.addEventListener('objectChange', () => {
                       this.syncDisplay();
                    });

                    control.attach(currentMesh);
                    scene.add(control);

                    // Auto Resize handler via ResizeObserver
                    const resizeObserver = new ResizeObserver(entries => {
                        for (let entry of entries) {
                            const { width, height } = entry.contentRect;
                            if(width > 0 && height > 0 && camera && renderer) {
                                camera.aspect = width / height;
                                camera.updateProjectionMatrix();
                                renderer.setSize(width, height);
                                this.renderScene();
                            }
                        }
                    });
                    resizeObserver.observe(container);

                    camera.lookAt(0, 0, 0);

                    // Inisialisasi posisi berdasar livewire state aktif
                    this.loadFromWire();
                    this.renderScene();

                    // Polling reguler (5x/detik) untuk 2-way sync form -> canvas secara efisien
                    setInterval(() => {
                        if(!this.ignoreWatch) this.loadFromWire();
                    }, 200);

                }, 200);
            },
            
            renderScene() {
                if(renderer && scene && camera) {
                    renderer.render(scene, camera);
                }
            },

            setSection(s) {
                this.activeSection = s;
                this.ignoreWatch = false; // Reset watcher pause state
                this.loadFromWire();
            },

            setMode(newMode) {
                this.mode = newMode;
                if(control && orbit) {
                   if (newMode === 'pan') {
                       control.visible = false;
                       control.enabled = false;
                       orbit.mouseButtons.LEFT = THREE.MOUSE.PAN;
                   } else {
                       control.visible = true;
                       control.enabled = true;
                       control.setMode(newMode);
                       orbit.mouseButtons.LEFT = THREE.MOUSE.ROTATE;
                   }
                }
                this.syncDisplay();
                this.renderScene();
            },

            parseJson(str, def) {
                if(!str) return def;
                try { return JSON.parse(str) || def; } catch(e) { return def; }
            },

            replaceMeshWithPrimitive(type) {
                if (type === 'custom') {
                    let group = new THREE.Group();
                    
                    // Pusat: Bola transparan/wireframe agar mudah ditebak volume-nya
                    let coreGeo = new THREE.SphereGeometry(1.2, 32, 32);
                    let coreMat = new THREE.MeshStandardMaterial({ color: 0x4f46e5, roughness: 0.2, metalness: 0.8, wireframe: true });
                    let core = new THREE.Mesh(coreGeo, coreMat);
                    group.add(core);

                    // Helper untuk menempelkan kotak kecil berwarna di kutubnya
                    let addMarker = (color, x, y, z) => {
                        let markerGeo = new THREE.BoxGeometry(0.5, 0.5, 0.5);
                        let markerMat = new THREE.MeshStandardMaterial({ color: color, roughness: 0.2, metalness: 0.1 });
                        let marker = new THREE.Mesh(markerGeo, markerMat);
                        marker.position.set(x, y, z);
                        core.add(marker); // attach to core so it responds to scale if needed
                    };

                    addMarker(0xff0000, 1.25, 0, 0); // Kanan (Merah -> Sumbu +X)
                    addMarker(0x00ff00, -1.25, 0, 0); // Kiri (Hijau -> Sumbu -X)
                    addMarker(0x0000ff, 0, 1.25, 0); // Atas (Biru -> Sumbu +Y)
                    addMarker(0xffff00, 0, -1.25, 0); // Bawah (Kuning -> Sumbu -Y)
                    addMarker(0x00ffff, 0, 0, 1.25); // Depan/Muka (Cyan -> Sumbu +Z)
                    addMarker(0xff00ff, 0, 0, -1.25); // Belakang/Punggung (Magenta -> Sumbu -Z)

                    this.replaceMesh(group);
                    return;
                }

                let geometry;
                switch(type) {
                    case 'sphere': geometry = new THREE.SphereGeometry(1.2, 64, 64); break;
                    case 'box': geometry = new THREE.BoxGeometry(1.5, 1.5, 1.5); break;
                    case 'octahedron': geometry = new THREE.OctahedronGeometry(1.5); break;
                    case 'torus': geometry = new THREE.TorusGeometry(1, 0.4, 32, 100); break;
                    case 'torus_knot': default: geometry = new THREE.TorusKnotGeometry(1, 0.3, 128, 32); break;
                }
                
                let material = new THREE.MeshStandardMaterial({ 
                    color: 0x4f46e5, roughness: 0.2, metalness: 0.8, wireframe: true 
                });
                let newMesh = new THREE.Mesh(geometry, material);
                
                this.replaceMesh(newMesh);
            },

            updateModel(type, customModelPath) {
                // Untuk admin panel, jika type adalah 'custom', kita ganti render visualnya dengan 'Helper Sphere' (tidak meload file gbl aslinya demi efisiensi)
                this.replaceMeshWithPrimitive(type);
            },

            replaceMesh(newMesh) {
                if (!scene) return;
                
                let oldPos = new THREE.Vector3();
                let oldRot = new THREE.Euler();
                let oldScale = new THREE.Vector3(1,1,1);
                
                if (currentMesh) {
                    oldPos.copy(currentMesh.position);
                    oldRot.copy(currentMesh.rotation);
                    oldScale.copy(currentMesh.scale);
                    
                    if (control) control.detach();
                    scene.remove(currentMesh);
                    if (currentMesh.geometry) currentMesh.geometry.dispose();
                    if (currentMesh.material) currentMesh.material.dispose();
                }
                
                currentMesh = newMesh;

                currentMesh.position.copy(oldPos);
                currentMesh.rotation.copy(oldRot);
                currentMesh.scale.copy(oldScale);
                
                scene.add(currentMesh);
                if (control) control.attach(currentMesh);
                this.renderScene();
            },

            loadFromWire() {
                try {
                    // Update Shape System dynamically
                    let objType = this.wire.data['3d_object_type'];
                    let objCustomData = this.wire.data['3d_model_custom'];
                    let customModelStr = typeof objCustomData === 'string' ? objCustomData : null;
                    
                    let modelHash = objType + '_' + customModelStr;
                    if (this.lastModelHash !== modelHash) {
                        this.lastModelHash = modelHash;
                        this.updateModel(objType, customModelStr);
                    }

                    // Update Transformation System
                    let p = this.wire.data['3d_pos_' + this.activeSection];
                    let r = this.wire.data['3d_rot_' + this.activeSection];
                    let s = this.wire.data['3d_scale_' + this.activeSection];
                    
                    let currentHash = p + '_' + r + '_' + s;
                    if(this.lastDataHash === currentHash) return;
                    this.lastDataHash = currentHash;
                    
                    let pos = this.parseJson(p, {x:0, y:0, z:0});
                    let rot = this.parseJson(r, {x:0, y:0, z:0});
                    let scale = parseFloat(s) || 1.0;

                    if(currentMesh) {
                        currentMesh.position.set(pos.x, pos.y, pos.z);
                        currentMesh.rotation.set(rot.x, rot.y, rot.z);
                        currentMesh.scale.set(scale, scale, scale);
                    }
                    
                    this.syncDisplay();
                    this.renderScene();
                } catch(e) {
                    console.error("3D Viewport read Error:", e);
                }
            },

            syncDisplay() {
                if(!currentMesh) return;
                
                if(this.mode === 'translate') {
                    this.currPos.x = currentMesh.position.x.toFixed(3);
                    this.currPos.y = currentMesh.position.y.toFixed(3);
                    this.currPos.z = currentMesh.position.z.toFixed(3);
                } else if (this.mode === 'rotate') {
                    this.currPos.x = currentMesh.rotation.x.toFixed(3);
                    this.currPos.y = currentMesh.rotation.y.toFixed(3);
                    this.currPos.z = currentMesh.rotation.z.toFixed(3);
                } else {
                    this.currPos.x = currentMesh.scale.x.toFixed(3);
                    this.currPos.y = currentMesh.scale.y.toFixed(3);
                    this.currPos.z = currentMesh.scale.z.toFixed(3);
                }
            },

            onDragEnd() {
                if(!currentMesh) return;
                
                let pStr = JSON.stringify({ 
                    x: parseFloat(currentMesh.position.x.toFixed(3)), 
                    y: parseFloat(currentMesh.position.y.toFixed(3)), 
                    z: parseFloat(currentMesh.position.z.toFixed(3)) 
                });
                let rStr = JSON.stringify({ 
                    x: parseFloat(currentMesh.rotation.x.toFixed(3)), 
                    y: parseFloat(currentMesh.rotation.y.toFixed(3)), 
                    z: parseFloat(currentMesh.rotation.z.toFixed(3)) 
                });
                let sStr = parseFloat(currentMesh.scale.x.toFixed(3));

                if(this.mode === 'translate') this.wire.data['3d_pos_' + this.activeSection] = pStr;
                if(this.mode === 'rotate') this.wire.data['3d_rot_' + this.activeSection] = rStr;
                if(this.mode === 'scale') this.wire.data['3d_scale_' + this.activeSection] = sStr;

                this.lastDataHash = null;
                
                setTimeout(() => {
                    this.ignoreWatch = false;
                }, 500);
            }
        };
    });
});
</script>
