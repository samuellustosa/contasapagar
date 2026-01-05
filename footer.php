</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // 1. Faz as mensagens de alerta sumirem sozinhas após 3 segundos
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function(alert) {
    setTimeout(function() {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 3000); // 3000 milissegundos = 3 segundos
  });

  // 2. Registra o Service Worker para o PWA
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