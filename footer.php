</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Registra o Service Worker para habilitar o PWA
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('sw.js')
        .then(reg => console.log('PWA: Service Worker registrado com sucesso!'))
        .catch(err => console.log('PWA: Erro ao registrar Service Worker:', err));
    });
  }
</script>
</body>
</html>