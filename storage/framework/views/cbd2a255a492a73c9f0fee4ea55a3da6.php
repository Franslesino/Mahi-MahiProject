<?php $__env->startSection('title', $material->judul ?? $material->title); ?>

<?php $__env->startSection('content'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap');
    
    :root {
        --primary: #0d9488;
        --primary-dark: #0f766e;
        --primary-light: #14b8a6;
        --success: #10b981;
        --success-dark: #059669;
        --info: #06b6d4;
        --neutral-50: #fafafa;
        --neutral-100: #f5f5f5;
        --neutral-200: #e5e5e5;
        --neutral-600: #525252;
        --neutral-700: #404040;
        --neutral-800: #262626;
        --neutral-900: #171717;
        --teal-50: #f0fdfa;
        --teal-100: #ccfbf1;
    }
    
    .material-view-page {
        font-family: 'Outfit', sans-serif;
        background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
        min-height: 100vh;
        padding: 2rem 1rem;
    }
    
    .material-container {
        max-width: 80rem;
        margin: 0 auto;
    }
    
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: white;
        color: var(--neutral-700);
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(13, 148, 136, 0.1);
        text-decoration: none;
        border: 2px solid var(--teal-100);
    }
    
    .back-link:hover {
        color: var(--primary);
        border-color: var(--primary);
        transform: translateX(-3px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
    }
    
    .completed-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #d1fae5;
        color: var(--success-dark);
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px solid var(--success);
    }
    
    .content-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
        padding: 2.5rem;
        border: 2px solid var(--teal-100);
    }
    
    .material-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        gap: 2rem;
        flex-wrap: wrap;
    }
    
    .material-info {
        flex: 1;
        min-width: 0;
    }
    
    .material-type-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        background: var(--teal-100);
        color: var(--primary-dark);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }
    
    .material-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }
    
    .material-description {
        font-size: 0.9375rem;
        color: var(--neutral-600);
        line-height: 1.6;
    }
    
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-secondary {
        background: var(--neutral-100);
        color: var(--neutral-700);
        border: 2px solid var(--neutral-200);
    }
    
    .btn-secondary:hover {
        background: var(--neutral-200);
        transform: translateY(-1px);
    }
    
    .btn-info {
        background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
    }
    
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(6, 182, 212, 0.4);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.4);
    }
    
    .btn-success {
        background: #d1fae5;
        color: var(--success-dark);
        border: 2px solid var(--success);
    }
    
    .viewer-container {
        border-radius: 16px;
        border: 2px solid var(--teal-100);
        background: #000;
        overflow: hidden;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .viewer-container video,
    .viewer-container iframe {
        width: 100%;
        height: 100%;
        min-height: 500px;
    }
    
    .viewer-placeholder {
        color: rgba(255, 255, 255, 0.6);
        font-size: 1rem;
    }
    
    .content-viewer {
        padding: 2rem;
        background: white;
        color: var(--neutral-800);
        line-height: 1.8;
        min-height: 400px;
    }
    
    .quiz-prompt {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, var(--teal-50) 0%, white 100%);
    }
    
    .quiz-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        box-shadow: 0 8px 20px rgba(13, 148, 136, 0.3);
    }
    
    .quiz-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--neutral-800);
    }
    
    @media (max-width: 768px) {
        .material-view-page {
            padding: 1rem 0.5rem;
        }
        
        .content-card {
            padding: 1.5rem;
        }
        
        .material-title {
            font-size: 1.5rem;
        }
        
        .material-header {
            flex-direction: column;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .content-card {
        animation: fadeIn 0.5s ease;
    }
</style>

<div class="material-view-page">
    <div class="material-container">
        <!-- Header Bar -->
        <div class="header-bar">
            <a href="<?php echo e(route('student.course.learn', $course)); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke daftar materi</span>
            </a>
            
            <?php if($completed): ?>
                <div class="completed-indicator">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content Card -->
        <div class="content-card">
            <!-- Material Header -->
            <div class="material-header">
                <div class="material-info">
                    <span class="material-type-badge"><?php echo e(ucfirst($material->type)); ?></span>
                    <h1 class="material-title"><?php echo e($material->judul ?? $material->title); ?></h1>
                    <?php if($material->description): ?>
                        <p class="material-description"><?php echo e($material->description); ?></p>
                    <?php endif; ?>
                </div>

                <div class="action-buttons">
                    <?php
                        $downloadUrl = route('courses.materials.download', [$course, $material->id]);
                        $hasFile = in_array($material->type, ['video', 'pdf']) && ($material->file_url_full ?? $material->url_konten);
                    ?>
                    
                    <?php if($hasFile): ?>
                        <button type="button" 
                                class="btn btn-secondary" 
                                onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i>
                            Fullscreen
                        </button>
                        
                        <a href="<?php echo e($downloadUrl); ?>" class="btn btn-info">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                    <?php endif; ?>
                    
                    <?php if(!$completed): ?>
                        <form action="<?php echo e(route('courses.materials.complete', [$course, $material->id])); ?>" 
                              method="POST"
                              onsubmit="return handleMarkComplete(event)">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i>
                                Tandai Selesai
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="btn btn-success">
                            <i class="fas fa-check-circle"></i>
                            Sudah Selesai
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Material Viewer -->
            <div id="materialViewer" class="viewer-container">
                <?php if($material->type === 'video'): ?>
                    <?php
                        $videoSrc = $material->video_url ?? $material->file_url_full ?? $material->url_konten;
                        $mimeType = 'video/mp4';
                        if ($videoSrc && \Illuminate\Support\Str::endsWith(strtolower($videoSrc), ['.mov'])) {
                            $mimeType = 'video/quicktime';
                        }
                    ?>
                    
                    <?php if($videoSrc): ?>
                        <video controls playsinline style="width: 100%; height: auto; min-height: 500px;">
                            <source src="<?php echo e($videoSrc); ?>" type="<?php echo e($mimeType); ?>">
                            Browser Anda tidak mendukung pemutaran video.
                        </video>
                    <?php else: ?>
                        <p class="viewer-placeholder">Video belum tersedia</p>
                    <?php endif; ?>
                    
                <?php elseif($material->type === 'pdf'): ?>
                    <?php $pdfSrc = $material->file_url_full ?? $material->url_konten; ?>
                    
                    <?php if($pdfSrc): ?>
                        <iframe src="<?php echo e($pdfSrc); ?>" 
                                frameborder="0"
                                style="width: 100%; height: 100%; min-height: 600px;">
                        </iframe>
                    <?php else: ?>
                        <p class="viewer-placeholder">PDF belum tersedia</p>
                    <?php endif; ?>
                    
                <?php elseif($material->type === 'quiz'): ?>
                    <div class="quiz-prompt">
                        <div class="quiz-icon">
                            <i class="fas fa-clipboard-question"></i>
                        </div>
                        <p class="quiz-text">Siap untuk mengerjakan quiz?</p>
                        <a href="<?php echo e(route('courses.materials.quiz', [$course, $material->id])); ?>" 
                           class="btn btn-primary"
                           style="padding: 1rem 2rem; font-size: 1rem;">
                            <i class="fas fa-play"></i>
                            Mulai Quiz
                        </a>
                    </div>
                    
                <?php else: ?>
                    <div class="content-viewer">
                        <?php echo nl2br(e($material->content ?? $material->description ?? 'Konten belum tersedia')); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fullscreen toggle
function toggleFullscreen() {
    const viewer = document.getElementById('materialViewer');
    if (!viewer) return;

    if (!document.fullscreenElement) {
        if (viewer.requestFullscreen) {
            viewer.requestFullscreen();
        } else if (viewer.webkitRequestFullscreen) {
            viewer.webkitRequestFullscreen();
        } else if (viewer.msRequestFullscreen) {
            viewer.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

// Handle mark complete with feedback
function handleMarkComplete(event) {
    const button = event.target.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    }
    return true;
}

// Auto-mark video as complete when watched (optional feature)
document.addEventListener('DOMContentLoaded', () => {
    const video = document.querySelector('video');
    if (video) {
        let watched = false;
        video.addEventListener('timeupdate', () => {
            const percentage = (video.currentTime / video.duration) * 100;
            if (percentage > 90 && !watched) {
                watched = true;
                // Optional: Auto-suggest marking as complete
                console.log('Video almost complete - suggest marking as done');
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sabil Aditia\OneDrive\mahi\Mahi-MahiProject\resources\views/student/material-view.blade.php ENDPATH**/ ?>