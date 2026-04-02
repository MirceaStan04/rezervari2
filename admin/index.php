<?php
require_once __DIR__ . '/../includes/config.php';
require '../includes/layout.php';
require 'admin_check.php';

render_header('Panou Administrator');
?>

<section class="mb-4">
    <div class="hero-surface">
        <span class="badge-soft bg-white bg-opacity-10 text-white border border-white border-opacity-10 mb-3"><i class="bi bi-shield-lock"></i> Panou administrator</span>
        <h1 class="hero-title mb-2">Administrare rapidă a platformei</h1>
        <p class="hero-subtitle mb-0">Gestionezi locațiile, evenimentele, rezervările și utilizatorii dintr-o interfață mai clară și mai aerisită.</p>
    </div>
</section>

<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="soft-card p-4 h-100 text-center app-card">
            <span class="feature-icon mb-3"><i class="bi bi-building"></i></span>
            <h2 class="h5 fw-bold">Locații</h2>
            <p class="text-muted">Adaugă, verifică și organizează spațiile disponibile.</p>
            <a href="locatii.php" class="btn btn-primary w-100">Gestionare</a>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="soft-card p-4 h-100 text-center app-card">
            <span class="feature-icon mb-3"><i class="bi bi-calendar-event"></i></span>
            <h2 class="h5 fw-bold">Evenimente</h2>
            <p class="text-muted">Vezi programările, intervalele și locațiile asociate.</p>
            <a href="evenimente.php" class="btn btn-primary w-100">Gestionare</a>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="soft-card p-4 h-100 text-center app-card">
            <span class="feature-icon mb-3"><i class="bi bi-journal-check"></i></span>
            <h2 class="h5 fw-bold">Rezervări</h2>
            <p class="text-muted">Verifică cererile trimise și statusul fiecăreia.</p>
            <a href="rezervari.php" class="btn btn-primary w-100">Vizualizare</a>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="soft-card p-4 h-100 text-center app-card">
            <span class="feature-icon mb-3"><i class="bi bi-people"></i></span>
            <h2 class="h5 fw-bold">Utilizatori</h2>
            <p class="text-muted">Listă completă cu nume, email și rol pentru fiecare cont.</p>
            <a href="utilizatori.php" class="btn btn-primary w-100">Vezi lista</a>
        </div>
    </div>
</div>

<?php render_footer(); ?>
