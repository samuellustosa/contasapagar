</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // 1. Faz as mensagens de alerta sumirem sozinhas após 3 segundos
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function(alert) {
    setTimeout(function() {
      const bsAlert = new bootstrap.Alert(alert);
      if (alert) {
        bsAlert.close();
      }
    }, 3000); // 3000 milissegundos = 3 segundos
  });

  // 2. Lógica do Skeleton Screen
  // Seleciona links de navegação, botões de setas e o botão flutuante (+)
  const navLinks = document.querySelectorAll('.nav-link, .btn-outline-primary, .fab');
  const skeleton = document.getElementById('skeleton-loader');

  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Não ativa o skeleton se for o botão de excluir (que é perigoso e rápido)
      if (!this.classList.contains('btn-outline-danger')) {
        if (skeleton) {
          // Mostra o bloco de skeleton que você colocou no index.php
          skeleton.classList.remove('d-none');
          
          // Opcional: esconde o conteúdo antigo para o efeito ficar mais limpo
          const mainContent = document.querySelector('.row, .card, .table-responsive');
          if (mainContent) mainContent.style.opacity = '0.3';
        }
      }
    });
  });

  // 3. Registra o Service Worker para o PWA
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('sw.js')
        .then(reg => console.log('PWA: Service Worker registrado!'))
        .catch(err => console.log('PWA: Erro no SW:', err));
    });
  }
</script>

</body>
</html>