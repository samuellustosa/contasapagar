</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const skeleton = document.getElementById('skeleton-loader');
    const mainContent = document.getElementById('main-content');

    // Função para mostrar o loader e esconder o conteúdo
    function showLoader() {
      if (skeleton && mainContent) {
        skeleton.classList.remove('d-none');
        mainContent.classList.add('content-hidden');
        window.scrollTo(0, 0);
      }
    }

    // 1. Fechamento automático de alertas
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 3000);
    });

    // 2. Ativar Skeleton ao clicar em links (com as exceções)
    document.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Filtra links que NÃO devem disparar o loader
        if (href && !href.startsWith('#') && 
            !this.classList.contains('btn-outline-danger') && 
            !href.includes('pagar') && 
            !href.includes('excluir') &&
            !href.includes('relatorio-geral-pdf')) { // ADICIONADO: Não trava no PDF
          showLoader();
        }
      });
    });

    // Ativar Skeleton ao enviar formulários
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function() {
        showLoader();
      });
    });
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