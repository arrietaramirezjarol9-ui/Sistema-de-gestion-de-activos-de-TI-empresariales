@extends('layouts.app')

@section('title', 'Registrar Activo')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <a href="{{ route('activos.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver al Inventario
            </a>
            <h2 class="fw-bold mb-1">Registrar Activo TI</h2>
            <p class="text-muted">Complete los datos para agregar un nuevo activo al inventario.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card">
                <form action="{{ route('activos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="nombre" class="form-label fw-semibold">Nombre del Activo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Laptop Lenovo ThinkPad L14" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="categoria" class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                                <option value="" disabled selected>Seleccione una categoría</option>
                                <option value="Laptop" {{ old('categoria') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                <option value="PC" {{ old('categoria') == 'PC' ? 'selected' : '' }}>PC</option>
                                <option value="Impresora" {{ old('categoria') == 'Impresora' ? 'selected' : '' }}>Impresora</option>
                                <option value="Celular" {{ old('categoria') == 'Celular' ? 'selected' : '' }}>Celular</option>
                                <option value="Accesorio" {{ old('categoria') == 'Accesorio' ? 'selected' : '' }}>Accesorio</option>
                            </select>
                            @error('categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="marca" class="form-label fw-semibold">Marca *</label>
                            <input type="text" name="marca" id="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}" placeholder="Ej: Lenovo" required>
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="modelo" class="form-label fw-semibold">Modelo *</label>
                            <input type="text" name="modelo" id="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}" placeholder="Ej: ThinkPad L14 Gen 3" required>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="numero_serie" class="form-label fw-semibold">Número de Serie *</label>
                            <input type="text" name="numero_serie" id="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" value="{{ old('numero_serie') }}" placeholder="Ej: SN-492984920" required>
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="estado" class="form-label fw-semibold">Estado Inicial *</label>
                            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="Disponible" {{ old('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="Mantenimiento" {{ old('estado') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="De Baja" {{ old('estado') == 'De Baja' ? 'selected' : '' }}>De Baja</option>
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
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio') }}" placeholder="Ej: 2850.00" required>
                            </div>
                            @error('precio')
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="fecha_compra" class="form-label fw-semibold">Fecha de Compra *</label>
                            <input type="date" name="fecha_compra" id="fecha_compra" class="form-control @error('fecha_compra') is-invalid @enderror" value="{{ old('fecha_compra', date('Y-m-d')) }}" required>
                            @error('fecha_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold">Descripción / Observaciones</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Agregue especificaciones técnicas del activo como RAM, almacenamiento, accesorios incluidos, etc.">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="imagen" class="form-label fw-semibold">Fotografía / Imagen del Activo</label>
                        <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Formatos admitidos: JPG, PNG, WEBP (Máx. 2MB)</small>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('activos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Guardar Activo</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
