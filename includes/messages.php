<?php 
$msgError   = $errorMessage ?? $error ?? "";
$msgSuccess = $successMessage ?? $success ?? "";
?>

<?php if ($msgError): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
        <div>
            <strong class="d-block">Hoppla!</strong>
            <span class="small"><?php echo htmlspecialchars($msgError); ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if ($msgSuccess): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
        <div>
            <strong class="d-block">Erfolg!</strong>
            <span class="small"><?php echo htmlspecialchars($msgSuccess); ?></span>
        </div>
    </div>
<?php endif; ?>