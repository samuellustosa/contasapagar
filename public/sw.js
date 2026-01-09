const CACHE_NAME = 'contas-v4'; 
const ASSETS = [
  '/', 
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'icon-192x192.png',
  'icon-512x512.png',
  'manifest.json'
];

// Instalação e Cache
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('PWA: Arquivos cacheados com sucesso');
      return cache.addAll(ASSETS);
    })
  );
  self.skipWaiting(); // Força o novo SW a assumir o controle imediatamente
});

// Limpeza de caches antigos
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME)
            .map(key => caches.delete(key))
      );
    })
  );
});

// Estratégia: Network First (Tenta rede, se falhar usa cache)
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});