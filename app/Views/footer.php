</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const skeleton = document.getElementById('skeleton-loader');
    const mainContent = document.getElementById('main-content');

    // Função para mostrar o loader e esconder o conteúdo
    function showLoader() {
      if (skeleton && mainContent) {
        skeleton.classList.remove('d-none'); // Remove a classe que esconde o skeleton
        mainContent.classList.add('content-hidden'); // Adiciona a classe que esconde o conteúdo real
        window.scrollTo(0, 0); // Garante que a transição comece do topo da página
      }
    }

    // 1. Fechamento automático de alertas (mensagens de sucesso/erro)
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 3000); // Reduzido para 3 segundos para uma resposta mais rápida
    });

    // 2. Ativar Skeleton ao clicar em links (navegação interna)
    document.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Filtra links que não devem disparar o loader (âncoras, exclusões e botões de pagamento)
        if (href && !href.startsWith('#') && 
            !this.classList.contains('btn-outline-danger') && 
            !href.includes('pagar') && 
            !href.includes('excluir')) {
          showLoader();
        }
      });
    });


    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function() {
        showLoader();
      });
    });

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