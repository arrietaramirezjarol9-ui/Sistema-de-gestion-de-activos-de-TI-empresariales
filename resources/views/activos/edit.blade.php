@extends('layouts.app')

@section('title', 'Editar Activo')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <a href="{{ route('activos.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver al Inventario
            </a>
            <h2 class="fw-bold mb-1">Editar Activo TI</h2>
            <p class="text-muted">Modifique los datos del activo: <strong>{{ $activo->codigo_qr }}</strong></p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card">
                <form action="{{ route('activos.update', $activo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="nombre" class="form-label fw-semibold">Nombre del Activo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $activo->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="categoria" class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                                <option value="Laptop" {{ old('categoria', $activo->categoria) == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                <option value="PC" {{ old('categoria', $activo->categoria) == 'PC' ? 'selected' : '' }}>PC</option>
                                <option value="Impresora" {{ old('categoria', $activo->categoria) == 'Impresora' ? 'selected' : '' }}>Impresora</option>
                                <option value="Celular" {{ old('categoria', $activo->categoria) == 'Celular' ? 'selected' : '' }}>Celular</option>
                                <option value="Accesorio" {{ old('categoria', $activo->categoria) == 'Accesorio' ? 'selected' : '' }}>Accesorio</option>
                            </select>
                            @error('categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="marca" class="form-label fw-semibold">Marca *</label>
                            <input type="text" name="marca" id="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $activo->marca) }}" required>
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="modelo" class="form-label fw-semibold">Modelo *</label>
                            <input type="text" name="modelo" id="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $activo->modelo) }}" required>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="numero_serie" class="form-label fw-semibold">Número de Serie *</label>
                            <input type="text" name="numero_serie" id="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" value="{{ old('numero_serie', $activo->numero_serie) }}" required>
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="estado" class="form-label fw-semibold">Estado *</label>
                            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="Disponible" {{ old('estado', $activo->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="Asignado" {{ old('estado', $activo->estado) == 'Asignado' ? 'selected' : '' }} {{ $activo->estado !== 'Asignado' ? 'disabled' : '' }}>Asignado (Solo modificable vía préstamo)</option>
                                <option value="Mantenimiento" {{ old('estado', $activo->estado) == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="De Baja" {{ old('estado', $activo->estado) == 'De Baja' ? 'selected' : '' }}>De Baja</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="precio" class="form-label fw-semibold">Precio de Compra (S/) *</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $activo->precio) }}" required>
                            </div>
                            @error('precio')
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="fecha_compra" class="form-label fw-semibold">Fecha de Compra *</label>
                            <input type="date" name="fecha_compra" id="fecha_compra" class="form-control @error('fecha_compra') is-invalid @enderror" value="{{ old('fecha_compra', $activo->fecha_compra ? $activo->fecha_compra->format('Y-m-d') : '') }}" required>
                            @error('fecha_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold">Descripción / Observaciones</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $activo->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="imagen" class="form-label fw-semibold">Fotografía / Imagen del Activo</label>
                        @if($activo->imagen)
                            <div class="mb-2">
                                <img src="{{ asset($activo->imagen) }}" class="rounded shadow-sm border" style="max-height: 120px; object-fit: cover;" alt="Imagen del activo">
                                <small class="text-muted d-block">Imagen actual</small>
                            </div>
                        @endif
                        <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Deje en blanco para conservar la imagen actual. JPG, PNG, WEBP (Máx. 2MB)</small>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('activos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
