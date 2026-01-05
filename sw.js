const CACHE_NAME = 'contas-v2'; // Mudamos para v2 para forçar a atualização
const ASSETS = [
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'icon-192x192.png',
  'icon-512x512.png',
  'manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
  );
});

self.addEventListener('fetch', event => {
  // Estratégia: Tenta sempre a rede primeiro para garantir dados atualizados.
  // Se estiver sem internet, ele tenta buscar o que tem no cache.
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});