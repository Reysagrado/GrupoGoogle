<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Estado Civil
                </span>
                <a href="<?= base_url('estadocivil') ?>" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Ficha
                </a>
            </div>

            <form method="post" action="<?= base_url('estadocivil/store') ?>">
                <?= csrf_field() ?>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre del Estado Civil <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre" value="<?= esc($old['nombre'] ?? old('nombre')) ?>"  placeholder="Ej: Soltero/a, Casado/a, Unión Libre..." maxlength="50" required autofocus>
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nombre']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Ingrese la denominación del estado civil.</div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="<?= base_url('estadocivil') ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Guardar Estado Civil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
