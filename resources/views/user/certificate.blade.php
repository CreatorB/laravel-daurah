@extends('layouts.app')

@section('title', 'Sertifikat - ' . $event->nama_event)

@section('content')
<style>
@media (max-width: 575.98px) {
    .certificate-page #cert-wrapper { padding: 5px !important; }
    .certificate-page #cert-container { max-width: 100%; overflow-x: auto; }
    .certificate-page .form-range { width: 100%; }
    .certificate-page .btn { width: 100%; margin-bottom: 0.5rem; }
    .certificate-page h4 { font-size: 1.25rem; }
    .certificate-page .form-label { font-size: 0.875rem; }
}
</style>
<div class="container text-center py-4 certificate-page">
    <div class="card shadow border-0 mb-4 mx-auto" style="max-width: 100%;">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Editor Sertifikat</h4>

            <div class="row justify-content-center mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Edit Nama:</label>
                    <input type="text" id="input-name" class="form-control text-center mb-2" value="{{ $user->nama }}">
                    
                    <label class="form-label fw-bold">Ukuran Font:</label>
                    <input type="range" id="input-size" class="form-range" min="10" max="100" value="{{ $event->cert_font_size ?? 30 }}">

                    <label class="form-label fw-bold">Jarak Kata:</label>
                    <input type="range" id="input-word-spacing" class="form-range" min="0" max="50" value="0">
                    
                    <small class="text-muted d-block">Ubah nama, ukuran font, atau jarak kata sesuai keinginan.</small>
                </div>
            </div>

            <div class="alert alert-info py-2 small">
                <i class="fa fa-info-circle"></i> Geser nama di gambar bawah ini ke posisi yang pas.
            </div>

            <div id="cert-wrapper" style="width:100%;overflow:hidden;position:relative;background:#666;padding:10px;display:flex;justify-content:center;">
                <div id="cert-container" style="position:relative;display:inline-block;box-shadow:0 4px 8px rgba(0,0,0,0.5);background:white;">
<img id="cert-image" src="{{ $event->cert_template }}" alt="Sertifikat" style="display:block;pointer-events:none;">
                    <div id="draggable-name" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:{{ $event->cert_font_size ?? 30 }}px;color:{{ $event->cert_font_color ?? '#000000' }};font-weight:bold;white-space:nowrap;cursor:move;border:2px dashed rgba(255,0,0,0.5);padding:5px;user-select:none;z-index:10;">
                        {{ $user->nama }}
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary me-2">Kembali</a>
                <button id="btn-download" class="btn btn btn-primary gradient-bg fw-bold px-4">
                    <i class="fa fa-download me-2"></i>Download Sertifikat
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    const dragName = document.getElementById('draggable-name');
    const container = document.getElementById('cert-container');
    const img = document.getElementById('cert-image');
    const inputName = document.getElementById('input-name');
    const inputSize = document.getElementById('input-size');
    const inputWordSpacing = document.getElementById('input-word-spacing');
    const wrapper = document.getElementById('cert-wrapper');

    inputName.addEventListener('input', function(){
        dragName.innerText = this.value;
    });

    inputSize.addEventListener('input', function(){
        dragName.style.fontSize = this.value + 'px';
    });

    inputWordSpacing.addEventListener('input', function(){
        dragName.style.wordSpacing = this.value + 'px';
    });

    function resizeCert() {
        container.style.transform = 'none';
        let naturalWidth = img.naturalWidth || img.width;
        let naturalHeight = img.naturalHeight || img.height;
        if (naturalWidth === 0) return;
        container.style.width = naturalWidth + 'px';
        container.style.height = naturalHeight + 'px';
        let wrapperWidth = wrapper.clientWidth - 20;
        let scaleStr = 1;
        if (naturalWidth > wrapperWidth) {
            scaleStr = wrapperWidth / naturalWidth;
        }
        container.style.transform = 'scale(' + scaleStr + ')';
        wrapper.style.height = (naturalHeight * scaleStr) + 'px';
    }

    img.onload = resizeCert;
    window.addEventListener('resize', resizeCert);
    if (img.complete) resizeCert();

    let isDragging = false;
    let startX, startY, initialLeft, initialTop;

    function startDrag(e) {
        isDragging = true;
        let clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        let clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
        startX = clientX;
        startY = clientY;
        const style = window.getComputedStyle(dragName);
        initialLeft = parseFloat(style.left) || 0;
        initialTop = parseFloat(style.top) || 0;
        dragName.style.cursor = 'grabbing';
    }

    function doDrag(e) {
        if (!isDragging) return;
        e.preventDefault();
        let clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        let clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
        let deltaX = clientX - startX;
        let deltaY = clientY - startY;
        let currentScale = 1;
        let match = container.style.transform.match(/scale\(([^)]+)\)/);
        if (match && match[1]) currentScale = parseFloat(match[1]);
        let realDeltaX = deltaX / currentScale;
        let realDeltaY = deltaY / currentScale;
        dragName.style.left = (initialLeft + realDeltaX) + 'px';
        dragName.style.top = (initialTop + realDeltaY) + 'px';
        dragName.style.transform = 'none';
    }

    function stopDrag() {
        isDragging = false;
        dragName.style.cursor = 'move';
    }

    dragName.addEventListener('mousedown', startDrag);
    document.addEventListener('mousemove', doDrag);
    document.addEventListener('mouseup', stopDrag);
    dragName.addEventListener('touchstart', startDrag);
    document.addEventListener('touchmove', doDrag);
    document.addEventListener('touchend', stopDrag);

    document.getElementById('btn-download').addEventListener('click', () => {
        container.classList.add('clean-mode');
        html2canvas(container, {
            scale: 2,
            useCORS: true,
            onclone: (clonedDoc) => {
                let clonedContainer = clonedDoc.getElementById('cert-container');
                clonedContainer.style.transform = 'none';
            }
        }).then(canvas => {
            container.classList.remove('clean-mode');
            let link = document.createElement('a');
            link.download = 'Sertifikat-{{ str_replace(" ", "-", $user->nama) }}.png';
            link.href = canvas.toDataURL("image/png");
            link.click();
        });
    });
</script>
@endpush
@endsection
