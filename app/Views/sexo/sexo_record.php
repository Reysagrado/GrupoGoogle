<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Barra de Navegación de Registros -->
<?= view('layout/nav_toolbar', [
    'module'    => 'sexo',
    'currentId' => $currentId,
    'posicion'  => $posicion,
]) ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <?php if (!$sexo): ?>
            <div class="card p-5 text-center">
                <i class="bi bi-gender-ambiguous display-3 text-muted mb-3"></i>
                <h4 class="text-dark">No hay registros de Sexo</h4>
                <p class="text-secondary">Ingrese un nuevo registro para comenzar.</p>
                <div>
                    <a href="<?= base_url('sexo/add') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Crear Registro
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span class="fw-bold">
                        <i class="bi bi-gender-ambiguous me-2"></i>Ficha de Sexo #<?= esc($sexo['idsexo']) ?>
                    </span>
                    <span class="badge bg-dark text-white">Catálogo</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-3 border-bottom mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 text-warning mb-2">
                            <i class="bi bi-gender-ambiguous display-5"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-0"><?= esc($sexo['nombre']) ?></h3>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small text-uppercase">Código / ID:</dt>
                        <dd class="col-sm-8 fw-semibold">#<?= esc($sexo['idsexo']) ?></dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Nombre:</dt>
                        <dd class="col-sm-8 fs-5 fw-bold text-primary"><?= esc($sexo['nombre']) ?></dd>
                        <dt class="col-sm-4 text-muted small text-uppercase">Personas:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($totalPersonas ?? 0) ?></span>
                        </dd>
                    </dl>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="<?= base_url('sexo/edit/' . $sexo['idsexo']) ?>" class="btn btn-sm btn-outline-warning text-dark">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmarBorrado('<?= base_url('sexo/delete/' . $sexo['idsexo']) ?>', '¿Desea eliminar el sexo <?= esc($sexo['nombre']) ?>?')">
                        <i class="bi bi-trash-fill me-1"></i> Borrar
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
