</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const skeleton = document.getElementById('skeleton-loader');
    const mainContent = document.getElementById('main-content');

    function showLoader() {
      if (skeleton && mainContent) {
        skeleton.classList.remove('d-none');
        mainContent.classList.add('content-hidden');
      }
    }

    // 1. Auto-close alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 3500);
    });

    // 2. Ativar Skeleton em links (exceto ações de excluir/pagar)
    document.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && !href.startsWith('#') && 
            !this.classList.contains('btn-outline-danger') && 
            !href.includes('pagar') && 
            !href.includes('excluir')) {
          showLoader();
        }
      });
    });

    // 3. Ativar Skeleton ao enviar formulários
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function() {
        showLoader();
      });
    });

    // 4. Service Worker PWA
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/contasapagar/public/sw.js')
        .then(reg => console.log('PWA: Service Worker ativo'))
        .catch(err => console.error('PWA: Falha ao registrar SW', err));
    }
  });
</script>

<footer class="container mt-5 mb-4">
    <div class="border-top pt-3 text-center">
        <small class="text-secondary" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase;">
            Desenvolvido por <strong class="text-dark">Samuel Lustosa</strong>
        </small>
    </div>
</footer>

</body>
</html>