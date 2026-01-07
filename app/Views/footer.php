</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // 1. Faz as mensagens de alerta sumirem sozinhas com animação suave
  document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 3500); // 3.5 segundos para dar tempo de leitura
    });
  });

  // 2. Lógica do Skeleton Screen Otimizada
  const navLinks = document.querySelectorAll('.nav-link, .btn-outline-primary, .fab');
  const skeleton = document.getElementById('skeleton-loader');

  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Verifica se o link não é apenas um botão de ação (como excluir ou pagar)
      if (!this.classList.contains('btn-outline-danger') && !this.href.includes('pagar')) {
        if (skeleton) {
          skeleton.classList.remove('d-none');
          
          // Esconde o conteúdo principal para destacar o loading
          const mainContent = document.querySelector('.row, .card, .table-responsive');
          if (mainContent) {
            mainContent.style.transition = 'opacity 0.2s';
            mainContent.style.opacity = '0';
          }
        }
      }
    });
  });

  // 3. Registro do Service Worker para PWA (Melhorado com caminho dinâmico)
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      // O uso de "/" garante que o SW seja buscado na raiz do diretório público
      navigator.serviceWorker.register('/contasapagar/public/sw.js')
        .then(reg => console.log('PWA: Service Worker ativo para o escopo:', reg.scope))
        .catch(err => console.error('PWA: Falha ao registrar SW:', err));
    });
  }
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