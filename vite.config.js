import { defineConfig } from 'vite';
import postcss from 'rollup-plugin-postcss';

export default defineConfig({
  root: './assets', // Diretório raiz para os arquivos de entrada
  build: {
    outDir: './build', // Diretório de saída
    emptyOutDir: true,
    rollupOptions: {
      input: './css/tailwind.css', // Arquivo de entrada
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'tailwind.css') {
            return 'build/tailwind.css';
          }
          return assetInfo.name;
        },
      },
    },
  },
  plugins: [
    postcss({
      extract: true,
    }),
  ],
  server: {
    watch: {
      usePolling: true, // Necessário para hot reload em sistemas de arquivos como Docker
    },
    proxy: {
      // Proxy para o servidor PHP
      '/': 'http://localhost:8080', // Substitua pelo URL do seu servidor local
    },
  },
});