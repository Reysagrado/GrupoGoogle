<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Barra de Navegación de Registros -->
<?= view('layout/nav_toolbar', [
    'module'    => 'estadocivil',
    'currentId' => $currentId,
    'posicion'  => $posicion,
]) ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <?php if (!$estadoCivil): ?>
            <div class="card p-5 text-center">
                <i class="bi bi-heart display-3 text-muted mb-3"></i>
                <h4 class="text-dark">No hay registros de Estado Civil</h4>
                <p class="text-secondary">Ingrese un nuevo estado civil para comenzar.</p>
                <div>
                    <a href="<?= base_url('estadocivil/add') ?>" class="btn btn-danger">
                        <i class="bi bi-plus-circle me-1"></i> Crear Registro
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">
                        <i class="bi bi-heart-fill me-2"></i>Ficha de Estado Civil #<?= esc($estadoCivil['idestadocivil']) ?>
                    </span>
                    <span class="badge bg-light text-danger">Catálogo</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-3 border-bottom mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 text-danger mb-2">
                            <i class="bi bi-heart-fill display-5"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-0"><?= esc($estadoCivil['nombre']) ?></h3>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small text-uppercase">Código / ID:</dt>
                        <dd class="col-sm-8 fw-semibold">#<?= esc($estadoCivil['idestadocivil']) ?></dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Nombre:</dt>
                        <dd class="col-sm-8 fs-5 fw-bold text-danger"><?= esc($estadoCivil['nombre']) ?></dd>
                        
                        <dt class="col-sm-4 text-muted small text-uppercase">Personas:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-danger-subtle text-danger-emphasis"><?= esc($totalPersonas ?? 0) ?></span>
                        </dd>
                    </dl>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="<?= base_url('estadocivil/edit/' . $estadoCivil['idestadocivil']) ?>" class="btn btn-sm btn-outline-warning text-dark">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmarBorrado('<?= base_url('estadocivil/delete/' . $estadoCivil['idestadocivil']) ?>', '¿Desea eliminar el estado civil <?= esc($estadoCivil['nombre']) ?>?')">
                        <i class="bi bi-trash-fill me-1"></i> Borrar
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
