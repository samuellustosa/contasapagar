const CACHE_NAME = 'contas-v7'; // Versão atualizada para forçar a limpeza do erro anterior

const ASSETS = [
  '/',
  '/index.php',
  '/manifest.json',
  '/icon-192x192.png',
  '/icon-512x512.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
];

// Instalação: Tenta cachear, mas não trava se um arquivo falhar ou redirecionar
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      for (const url of ASSETS) {
        try {
          const response = await fetch(url);
          if (!response.ok) continue;
          
          // Se a resposta for redirecionada (ex: / -> /login), 
          // buscamos o conteúdo final para o cache não dar erro
          const responseToCache = response.redirected ? await fetch(response.url) : response;
          await cache.put(url, responseToCache.clone());
        } catch (err) {
          console.warn('Falha ao cachear recurso no install:', url);
        }
      }
    })
  );
});

// Ativação: Limpa caches antigos para evitar conflitos
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(
      keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
    ))
  );
  return self.clients.claim();
});

// Fetch: Trata o erro de redirecionamento em tempo real
self.addEventListener('fetch', event => {
  // Ignora esquemas que não sejam http ou https (como extensões do Chrome)
  if (!(event.request.url.indexOf('http') === 0)) return;

  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      // Se estiver no cache, entrega logo
      if (cachedResponse) return cachedResponse;

      // Se não estiver, busca na rede tratando redirecionamentos
      return fetch(event.request).then(response => {
        if (response.redirected) {
          // Se o servidor redirecionou, buscamos a URL final explicitamente
          return fetch(response.url);
        }
        return response;
      }).catch(() => {
        // Fallback offline para navegação principal
        if (event.request.mode === 'navigate') {
          return caches.match('/');
        }
      });
    })
  );
});