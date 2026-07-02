@extends('layouts.app')

@section('title', 'Escanear Activo QR')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <h2 class="fw-bold mb-1"><i class="bi bi-qr-code-scan text-primary me-2"></i>Escáner de Activos TI</h2>
            <p class="text-muted">Utilice la cámara de su dispositivo para escanear la etiqueta QR de un activo y acceder instantáneamente a su ficha de control.</p>
        </div>
    </div>

    <!-- Scanner Box -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card text-center">
                <h5 class="fw-bold mb-3">Lector de Cámara en Vivo</h5>
                
                <!-- Camera reader container -->
                <div class="bg-light p-3 rounded mb-4" style="border: 1px dashed var(--border-color);">
                    <div id="reader" class="mx-auto" style="width: 100%; max-width: 480px; border-radius: 12px; overflow: hidden; border: none; background: #000;"></div>
                </div>

                <!-- Status alert container -->
                <div id="scan-status" class="mb-2">
                    <div class="alert alert-light border shadow-sm p-3 mb-0" style="border-radius: 8px;">
                        <i class="bi bi-camera fs-4 text-primary d-block mb-1"></i>
                        <span class="fw-semibold d-block">Esperando permisos de cámara...</span>
                        <small class="text-muted">Por favor, conceda el acceso a la webcam cuando el navegador lo solicite.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
    /* Estilizar la UI interna de html5-qrcode para que se adapte al diseño premium */
    #reader {
        position: relative;
    }
    #reader__dashboard {
        padding: 15px !important;
        background-color: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        border-radius: 0 0 12px 12px !important;
    }
    #reader__dashboard_section_csr button {
        background-color: var(--primary-color) !important;
        color: white !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-weight: 550 !important;
        font-size: 0.85rem !important;
        cursor: pointer !important;
        transition: background-color 0.2s ease !important;
        margin: 5px !important;
    }
    #reader__dashboard_section_csr button:hover {
        background-color: var(--primary-hover) !important;
    }
    #reader__dashboard_section_csr select {
        padding: 8px 12px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        outline: none !important;
        font-size: 0.85rem !important;
    }
    #reader__camera_selection {
        margin-bottom: 10px !important;
    }
</style>
@endsection

@section('scripts')
<!-- Cargar librería html5-qrcode desde CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scanStatus = document.getElementById('scan-status');

        function onScanSuccess(decodedText, decodedResult) {
            // Sonido de éxito (opcional)
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // Beep agudo
                osc.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.1);
            } catch (e) {
                console.log("Audio not supported");
            }

            // Mostrar estado en pantalla
            scanStatus.innerHTML = `
                <div class="alert alert-success border-0 shadow-sm p-3 mb-0" style="border-radius: 8px;">
                    <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-1"></i>
                    <strong>¡Código detectado con éxito!</strong><br>
                    <small class="text-muted d-block mb-2">${decodedText}</small>
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Redirigiendo...
                </div>
            `;

            // Detener el lector
            html5QrcodeScanner.clear().then(_ => {
                // Redirigir al usuario
                if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                    window.location.href = decodedText;
                } else {
                    window.location.href = '/activos?search=' + encodeURIComponent(decodedText);
                }
            }).catch(err => {
                console.warn("Error deteniendo el escáner: ", err);
                if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                    window.location.href = decodedText;
                } else {
                    window.location.href = '/activos?search=' + encodeURIComponent(decodedText);
                }
            });
        }

        function onScanFailure(error) {
            // Se ejecuta al fallar un frame, ignoramos para continuar leyendo en vivo
        }

        // Inicializar escáner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 15, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            },
            /* verbose= */ false
        );

        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        // Actualizar el estado una vez que se inicia la cámara
        // html5-qrcode inserta elementos de control cuando tiene permisos
        const checkCameraInterval = setInterval(function() {
            const startBtn = document.getElementById('html5-qrcode-button-camera-start');
            const stopBtn = document.getElementById('html5-qrcode-button-camera-stop');
            
            if (startBtn || stopBtn) {
                scanStatus.innerHTML = `
                    <div class="alert alert-info border-0 shadow-sm p-3 mb-0" style="border-radius: 8px; background-color: rgba(6, 182, 212, 0.08); color: #0891b2;">
                        <i class="bi bi-qr-code-scan fs-4 d-block mb-1"></i>
                        <span class="fw-semibold d-block">Escáner Listo</span>
                        <small>Alinee el código QR del equipo dentro del cuadro verde de escaneo.</small>
                    </div>
                `;
                clearInterval(checkCameraInterval);
            }
        }, 500);
    });
</script>
@endsection
